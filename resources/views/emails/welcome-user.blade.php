@component('mail::message')
# Welcome to {{ config('app.name') }}!

Hello {{ $user->name }},

Welcome to {{ config('app.name') }}! We're thrilled to have you join our community.

Your account has been successfully created with the following credentials: 

- **Username/Email**: {{ $user->email }}
- **Password**: {{ $user->phone }} (If you haven't changed your password!)

## Getting Started

Here are a few things you can do to get started:

- **Explore your dashboard** - Discover all the features available to you
- **Complete your profile** - Add more details to personalize your experience

@component('mail::button', ['url' => $loginUrl, 'color' => 'success'])
    Get Started
@endcomponent

## Quick Tips

- **Keep your account secure** - Use a strong password and enable two-factor authentication
- **Stay updated** - Check your email regularly for important updates and tips
- **Join our community** - Connect with other users and share your experience

We're excited to see what you'll accomplish with {{ config('app.name') }}!

Regards,<br>
The {{ config('app.name') }} Team

@component('mail::subcopy')
    If you're having trouble with any of the buttons above, you can also visit us directly at [{{ config('app.url') }}]({{ config('app.url') }})
@endcomponent
@endcomponent
