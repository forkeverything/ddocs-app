<title>Files Collector: Be more pro</title>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="stripe-key" content="{{ env('STRIPE_KEY') }}"/>

<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('/images/icons/favicon.png') }}">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Scripts -->
<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>;
    window.awsURL = "{{awsURL()}}";
</script>


<!-- External Scripts -->
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
@include('vendor.trackers.fb-pixel')
@include('vendor.trackers.ga')
@include('vendor.fonts.type-kit')