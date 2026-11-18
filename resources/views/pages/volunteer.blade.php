<x-layout :title="$site->text('volunteer.title')" :description="$site->text('volunteer.description')">
<section class="section wrap support-form-section" aria-labelledby="volunteer-heading">
    <h1 id="volunteer-heading">{{ $site->text('volunteer.title') }}</h1>
    <x-enquiry-form :heading="$site->text('volunteer.form_heading')" :intro="$site->text('volunteer.intro')" default-type="Volunteering" :submit-label="$site->text('volunteer.submit_label')"/>
</section>
</x-layout>
