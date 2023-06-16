<!DOCTYPE html>
<html lang="fa-IR" class="no-js">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <title>{{ str_replace('-', ' ', strtoupper(config('app.name'))) }}'s API</title>
    <!-- Included CSS Files -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('landing-page/css/style.css') }}"/>
</head>
<body>
<div class="wrap flex">
    <!-- Main -->
    <div id="main">
        <div class="content">
            <!-- Modal page toggle -->
            <div style="text-align: center; font-weight: bold;">
                <h2>{{ str_replace('-', ' ', strtoupper(config('app.name'))) }}'s API</h2>
                <a target="_blank" href="{{ route('download-postman-collection') }}" class="button-about"
                   style="margin-left: 10px"><span class="fa fa-download"></span> دریافت کالکشن Postman </a>
                <a target="_blank" href="whatsapp://send?phone=+989122958172" class="button-about"><span
                        class="fa fa-support"></span> پشتیبانی </a><br><br>
                <blockquote class="app-version">نسخه {{ cache('git')['tag'] }} | هش آخرین
                    کامیت: {{ cache('git')['latest_commit_hash'] }} | تاریخ آخرین
                    کامیت: {{ \Illuminate\Support\Carbon::parse(cache('git')['latest_commit_time'])->diffForHumans() }}
                    | محیط نرم افزار: {{ cache('git')['branch'] }}</blockquote>
                <p class="subtitle"><span class="fa fa-code"></span> توسعه داده شده با <span class="fa fa-heart"
                                                                                             style="color: #ff0a00"></span>
                    توسط شرکت ورنا</p>
            </div>
        </div>
    </div>
</div>
<!-- Background overlay -->
<div class="body-bg"></div>
</body>
</html>
