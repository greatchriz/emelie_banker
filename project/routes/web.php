<?php
use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

  Route::redirect('admin', 'admin/login');
  Route::post('the/genius/ocean/2441139', 'Frontend\FrontendController@subscription');
  Route::get('finalize', 'Frontend\FrontendController@finalize');

  Route::get('/', 'Frontend\FrontendController@index')->name('front.index');
  
  Route::get('blogs', 'Frontend\FrontendController@blog')->name('front.blog');
  Route::get('blog/{slug}', 'Frontend\FrontendController@blogdetails')->name('blog.details');
  Route::get('/blog-search','Frontend\FrontendController@blogsearch')->name('front.blogsearch');
  Route::get('/blog/category/{slug}','Frontend\FrontendController@blogcategory')->name('front.blogcategory');
  Route::get('/blog/tag/{slug}','Frontend\FrontendController@blogtags')->name('front.blogtags');
  Route::get('/blog/archive/{slug}','Frontend\FrontendController@blogarchive')->name('front.blogarchive');

  Route::get('/services','Frontend\FrontendController@services')->name('front.services');

  Route::get('/about', 'Frontend\FrontendController@about')->name('front.about');
  Route::get('/contact', 'Frontend\FrontendController@contact')->name('front.contact');
  Route::post('/contact','Frontend\FrontendController@contactemail')->name('front.contact.submit');
  Route::get('/faq', 'Frontend\FrontendController@faq')->name('front.faq');
  Route::get('/{slug}','Frontend\FrontendController@page')->name('front.page');
  Route::post('/subscriber', 'Frontend\FrontendController@subscriber')->name('front.subscriber');

  Route::get('/currency/{id}', 'Frontend\FrontendController@currency')->name('front.currency');
  Route::get('/language/{id}', 'Frontend\FrontendController@language')->name('front.language');


