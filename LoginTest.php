<?php

use App\Models\User;
use function Pest\Laravel\get;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the login page')
    ->get('/login')
    ->assertStatus(200);

it('can log in Microsoft 365 users', function () {
    $user = User::factory()->create();
    $m365User = Mockery::mock('Laravel\Socialite\Two\User');
    $m365User->shouldReceive('getId')
        ->andReturn($user->ms_id);
    $provider = Mockery::mock('SocialiteProviders\Manager\ServiceProvider');
    $provider->shouldReceive('user')->andReturn($m365User);
    Socialite::shouldReceive('driver')->with('microsoft')->andReturn($provider);

    get('auth/ms365/callback')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

it('does not allow access m365 user if user is not in the database', function () {
    $m365User = Mockery::mock('Laravel\Socialite\Two\User');
    $m365User->shouldReceive('getId')
        ->andReturn('e27601ce-45df-324f-a101-82bcf00f9ff2');
    $provider = Mockery::mock('SocialiteProviders\Manager\ServiceProvider');
    $provider->shouldReceive('user')->andReturn($m365User);
    Socialite::shouldReceive('driver')->with('microsoft')->andReturn($provider);

    $response = $this->get('auth/ms365/callback')
        ->assertRedirect(route('login'));
    // Follow the redirects
    while ($response->isRedirect()) {
        $response = $this->get($response->headers->get('Location'));
    }
    $response->assertSee('Authorisation denied');

    $this->assertGuest();
});

it('can log user out from Microsoft 365', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->post('auth/logout')
        ->assertRedirect('https://login.microsoftonline.com/common/oauth2/v2.0/logout');
    $this->assertGuest();
});
