<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
</head>
<script>
    @if($token)
    var myToken = "{{ $token }}"
    axios.get(window.location.origin + '/sanctum/csrf-cookie', {
        headers: {'Accept': 'application/json'},
        withCredentials: true
    })
        .then((response) => {
            window.location.href = window.location.origin + `/ouath/${myToken}`
        })

    @else
    var error = {{ $error }}
        window.location.href = window.location.origin + '/signup'
    @endif
</script>
</html>
