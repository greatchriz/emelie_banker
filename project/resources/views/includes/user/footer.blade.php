<footer class="footer bg-n0">
  <div class="flex flex-col items-center justify-center gap-3 px-4 py-5 lg:flex-row lg:justify-between xxl:px-8">
    <p class="text-sm max-md:w-full max-md:text-center lg:text-base">
      {{ __('Copyright') }} @ <span id="current-year"></span>
      <a class="text-primary" href="{{ route('front.index') }}">{{ $gs->title }}</a>
    </p>
    <div class="justify-center max-md:flex max-md:w-full"></div>
    <ul class="flex gap-3 text-sm max-lg:w-full max-lg:justify-center lg:gap-4 lg:text-base">
      <li>
        <a href="{{ route('front.index') }}">{{ __('Home') }}</a>
      </li>
      <li>
        <a href="{{ route('user.message.index') }}">{{ __('Support') }}</a>
      </li>
    </ul>
  </div>
</footer>
