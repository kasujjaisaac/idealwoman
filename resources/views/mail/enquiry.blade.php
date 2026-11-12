<h1>Website enquiry</h1>
<p><strong>Name:</strong> {{ $enquiry['name'] }}</p>
<p><strong>Email:</strong> {{ $enquiry['email'] }}</p>
<p><strong>Organisation:</strong> {{ $enquiry['organisation'] ?? 'Not supplied' }}</p>
<p><strong>Enquiry type:</strong> {{ $enquiry['enquiry_type'] }}</p>
<p style="white-space: pre-wrap">{{ $enquiry['message'] }}</p>
