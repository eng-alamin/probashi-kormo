@props(['apply' => 'Apply Now'])
@use('App\Support\SafeRoute')
@use('App\Support\Site')
<div class="rounded-2xl bg-gradient-to-br from-pk-green to-pk-dark p-6 text-white shadow-xl">
  <h3 class="mb-2 text-lg font-semibold">Need help?</h3>
  <p class="mb-4 text-sm text-green-100">Our team will explain everything clearly.@if (Site::phone()) 📞 <a href="{{ Site::phoneHref() }}" class="font-semibold hover:underline">{{ Site::phone() }}</a>@endif</p>
  <a href="{{ SafeRoute::url('apply') }}" class="btn btn-red mb-3 block text-center">{{ $apply }}</a>
  <a href="{{ SafeRoute::url('contact') }}" class="btn btn-line block text-center">Contact Us</a>
</div>
