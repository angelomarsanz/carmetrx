<?php

namespace App\Traits\Tenant\Frontend;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

trait PageHeadings
{
  use Language;
  public function pageHeading($tenantId)
  {
    $language = $this->currentLang($tenantId);

    if (URL::current() == Route::is('frontend.aboutus')) {
      $pageHeading = $language->pageName()->select('about_page_title')->first();
    } elseif (URL::current() == Route::is('frontend.blog')) {
      $pageHeading = $language->pageName()->select('blog_page_title')->first();
    } else if (URL::current() == Route::is('frontend.faq')) {
      $pageHeading = $language->pageName()->select('faq_page_title')->first();
    } else if (URL::current() == Route::is('frontend.contact')) {
      $pageHeading = $language->pageName()->select('contact_page_title')->first();
    } else if (URL::current() == Route::is('frontend.agents')) {
      $pageHeading = $language->pageName()->select('agents_page_title')->first();
    } else if (URL::current() == Route::is('frontend.agent.forget.password')) {
      $pageHeading = $language->pageName()->select('agent_forget_password_page_title')->first();
    } else if (URL::current() == Route::is('frontend.agent.login')) {
      $pageHeading = $language->pageName()->select('agent_login_page_title')->first();
    } else if (URL::current() == Route::is('frontend.agent.login')) {
      $pageHeading = $language->pageName()->select('agent_forget_password_page_title')->first();
    } else if (URL::current() == Route::is('frontend.properties')) {
      $pageHeading = $language->pageName()->select('properties_page_title')->first();
    } else if (URL::current() == Route::is('frontend.projects')) {
      $pageHeading = $language->pageName()->select('projects_page_title')->first();
    } else if (URL::current() == Route::is('frontend.user.login')) {
      $pageHeading = $language->pageName()->select('login_page_title')->first();
    } else if (URL::current() == Route::is('frontend.user.signup')) {
      $pageHeading = $language->pageName()->select('signup_page_title')->first();
    } else if (URL::current() == Route::is('frontend.user.forget_password')) {
      $pageHeading = $language->pageName()->select('forget_password_page_title')->first();
    }

    return $pageHeading;
  }
}
