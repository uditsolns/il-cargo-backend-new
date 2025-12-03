<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GoogleMapsStaticService
{
    private $apiKey;
    private $baseUrl = 'https://maps.googleapis.com/maps/api/staticmap';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.key');

        if (empty($this->apiKey)) {
            throw new Exception('Google Maps API key is not configured');
        }
    }

    /**
     * Generate a static map image URL with multiple markers
     *
     * @param array $locations Array of ['lat' => float, 'lng' => float] or ['latitude' => float, 'longitude' => float]
     * @param array $options Additional map options
     * @return string Map image URL
     */
    public function generateMapWithMarkers(array $locations, array $options = []): string
    {
        if (empty($locations)) {
            throw new Exception('At least one location is required');
        }

        // Default options
        $defaultOptions = [
            'size' => '800x600',           // Map size (max 640x640 for free tier)
            'zoom' => null,                // Auto-zoom if null
            'maptype' => 'satellite',        // roadmap, satellite, hybrid, terrain
            'format' => 'png',             // png, png32, gif, jpg, jpg-baseline
            'scale' => 2,                  // 1 or 2 (for retina displays)
            'marker_color' => 'red',       // red, blue, green, purple, yellow, orange, etc.
            'marker_size' => 'mid',        // tiny, mid, small
            'marker_label' => null,        // A-Z, 0-9 (single character)
            'center' => null,              // Auto-center if null
            'style' => null,               // Custom map styling
        ];

        $options = array_merge($defaultOptions, $options);

        $params = [
            'size' => $options['size'],
            'maptype' => $options['maptype'],
            'format' => $options['format'],
            'scale' => $options['scale'],
            'key' => $this->apiKey,
        ];

        // Add zoom if specified
        if ($options['zoom']) {
            $params['zoom'] = $options['zoom'];
        }

        // Add center if specified
        if ($options['center']) {
            $params['center'] = $this->formatLatLng($options['center']);
        }

        // Add custom styling if provided
        if ($options['style']) {
            $params['style'] = $options['style'];
        }

        // Add markers
        $markers = $this->buildMarkersString($locations, $options);
        $params['markers'] = $markers;

        return $this->baseUrl . '?' . http_build_query($params);
    }

    /**
     * Generate map and save image to storage
     *
     * @param array $locations
     * @param string $filename
     * @param array $options
     * @return string Path to saved file
     */
    public function generateAndSaveMap(array $locations, string $filename, array $options = []): string
    {
        $mapUrl = $this->generateMapWithMarkers($locations, $options);

        $response = Http::timeout(30)->get($mapUrl);

        if (!$response->successful()) {
            throw new Exception('Failed to fetch map image: ' . $response->body());
        }

        $path = 'maps/' . $filename;
        \Storage::disk('public')->put($path, $response->body());

        return $path;
    }

    /**
     * Generate map with custom markers for different location types
     */
    public function generateMapWithCustomMarkers(array $locationGroups, array $options = []): string
    {
        if (empty($locationGroups)) {
            throw new Exception('At least one location group is required');
        }

        $defaultOptions = [
            'size' => '800x600',
            'zoom' => null,
            'maptype' => 'roadmap',
            'format' => 'png',
            'scale' => 1,
            'center' => null,
        ];

        $options = array_merge($defaultOptions, $options);

        $params = [
            'size' => $options['size'],
            'maptype' => $options['maptype'],
            'format' => $options['format'],
            'scale' => $options['scale'],
            'key' => $this->apiKey,
        ];

        if ($options['zoom']) {
            $params['zoom'] = $options['zoom'];
        }

        if ($options['center']) {
            $params['center'] = $this->formatLatLng($options['center']);
        }

        // Build markers - each group becomes a separate markers parameter
        $allMarkers = [];
        foreach ($locationGroups as $group) {
            $locations = $group['locations'] ?? [];
            $markerOptions = $group['options'] ?? [];

            if (!empty($locations)) {
                $allMarkers[] = $this->buildMarkersString($locations, $markerOptions);
            }
        }

        // Add all marker groups to params
        if (!empty($allMarkers)) {
            $params['markers'] = $allMarkers;
        }

        // Build the URL - handle multiple markers properly
        $url = $this->baseUrl . '?';
        $urlParts = [];

        foreach ($params as $key => $value) {
            if ($key === 'markers' && is_array($value)) {
                // Add each marker group as a separate markers parameter
                foreach ($value as $markerGroup) {
                    $urlParts[] = 'markers=' . urlencode($markerGroup);
                }
            } else {
                $urlParts[] = $key . '=' . urlencode($value);
            }
        }

        return $url . implode('&', $urlParts);
    }

    /**
     * Calculate the best center and zoom for given locations
     *
     * @param array $locations
     * @return array ['center' => ['lat' => float, 'lng' => float], 'zoom' => int]
     */
    public function calculateBestFit(array $locations): array
    {
        if (empty($locations)) {
            throw new Exception('Locations array cannot be empty');
        }

        if (count($locations) === 1) {
            $location = $this->normalizeLocation($locations[0]);
            return [
                'center' => $location,
                'zoom' => 6
            ];
        }

        $lats = [];
        $lngs = [];

        foreach ($locations as $location) {
            $normalized = $this->normalizeLocation($location);
            $lats[] = $normalized['lat'];
            $lngs[] = $normalized['lng'];
        }

        $centerLat = (min($lats) + max($lats)) / 2;
        $centerLng = (min($lngs) + max($lngs)) / 2;

        // Calculate approximate zoom based on the span
        $latSpan = max($lats) - min($lats);
        $lngSpan = max($lngs) - min($lngs);
        $maxSpan = max($latSpan, $lngSpan);

        // Rough zoom calculation
        $zoom = 1;
        if ($maxSpan < 0.01) $zoom = 7;
        elseif ($maxSpan < 0.02) $zoom = 7;
        elseif ($maxSpan < 0.05) $zoom = 7;
        elseif ($maxSpan < 0.1) $zoom = 7;
        elseif ($maxSpan < 0.2) $zoom = 7;
        elseif ($maxSpan < 0.5) $zoom = 6;
        elseif ($maxSpan < 1) $zoom = 6;
        elseif ($maxSpan < 2) $zoom = 6;
        elseif ($maxSpan < 5) $zoom = 6;
        elseif ($maxSpan < 10) $zoom = 6;
        else $zoom = 6;

        return [
            'center' => ['lat' => $centerLat, 'lng' => $centerLng],
            'zoom' => $zoom
        ];
    }

    /**
     * Build markers string for URL
     */
    private function buildMarkersString(array $locations, array $options): string
    {
        $markerParams = [];

        // Add marker styling
        if (!empty($options['marker_color'])) {
            $markerParams[] = 'color:' . $options['marker_color'];
        }

        if (!empty($options['marker_size'])) {
            $markerParams[] = 'size:' . $options['marker_size'];
        }

        if (!empty($options['marker_label'])) {
            $markerParams[] = 'label:' . $options['marker_label'];
        }

        // Add custom icon if provided
        if (!empty($options['marker_icon'])) {
            $markerParams[] = 'icon:' . urlencode($options['marker_icon']);
        }

        $markerString = '';
        if (!empty($markerParams)) {
            $markerString = implode('|', $markerParams) . '|';
        }

        // Add all locations
        $locationStrings = [];
        foreach ($locations as $location) {
            $locationStrings[] = $this->formatLatLng($location);
        }

        return $markerString . implode('|', $locationStrings);
    }

    /**
     * Normalize location array to consistent format
     */
    private function normalizeLocation(array $location): array
    {
        if (isset($location['lat']) && isset($location['lng'])) {
            return ['lat' => (float)$location['lat'], 'lng' => (float)$location['lng']];
        }

        if (isset($location['latitude']) && isset($location['longitude'])) {
            return ['lat' => (float)$location['latitude'], 'lng' => (float)$location['longitude']];
        }

        throw new Exception('Location must have lat/lng or latitude/longitude keys');
    }

    /**
     * Format lat/lng for URL
     */
    private function formatLatLng(array $location): string
    {
        $normalized = $this->normalizeLocation($location);
        return $normalized['lat'] . ',' . $normalized['lng'];
    }

    /**
     * Generate map image for inspection route specifically
     */
    public function generateInspectionRouteMap(array $locationData): string
    {
        if (empty($locationData)) {
            throw new Exception('Location data is required');
        }

        $locations = array_map(function ($location) {
            return [
                'lat' => (float)$location['latitude'],
                'lng' => (float)$location['longitude']
            ];
        }, $locationData);

        $bestFit = $this->calculateBestFit($locations);

        $options = [
            'size' => '800x400',
            'maptype' => 'roadmap',
            'zoom' => max(10, $bestFit['zoom']),
            'center' => $bestFit['center']
        ];

        // Create location groups with inspection-specific styling
        if (count($locations) === 1) {
            $locationGroups = [
                [
                    'locations' => $locations,
                    'options' => [
                        'marker_color' => 'green',
                        'marker_size' => 'mid',
                        'marker_label' => '1'
                    ]
                ]
            ];
        } else {
            $locationGroups = [];

            // Create separate marker group for each location
            foreach ($locations as $index => $location) {
                $isFirst = $index === 0;
                $isLast = $index === count($locations) - 1;

                $color = $isFirst ? 'green' : ($isLast ? 'red' : 'yellow');

                $locationGroups[] = [
                    'locations' => [$location],
                    'options' => [
                        'marker_color' => $color,
                        'marker_size' => 'mid',
                        'marker_label' => (string)($index + 1)
                    ]
                ];
            }
        }

        return $this->generateMapWithCustomMarkers($locationGroups, $options);
    }

    /**
     * Debug method - add this temporarily to see what URL is being generated
     */
    public function debugInspectionRouteMap(array $locationData): array
    {
        $locations = array_map(function ($location) {
            return [
                'lat' => (float)$location['latitude'],
                'lng' => (float)$location['longitude']
            ];
        }, $locationData);

        $url = $this->generateInspectionRouteMap($locationData);

        return [
            'processed_locations' => $locations,
            'generated_url' => $url,
            'location_count' => count($locations)
        ];
    }
}
