<!DOCTYPE html>
<html>
<head>
    <title>Reviewers Assigned</title>
</head>
<body>
    <h1>Reviewers Assigned</h1>
    <p>Hello {{ $project->user->firstname }},</p>
    <p>Reviewers have been assigned to your project <strong>{{ $project->title }}</strong>.</p>

    <h3>Assigned Reviewers:</h3>
    <ul>
        @foreach($reviewers as $name)
            <li>{{ $name }}</li>
        @endforeach
    </ul>

    <p><strong>Review Deadline:</strong> {{ \Carbon\Carbon::parse($deadline)->format('F j, Y') }}</p>

    <p>You can track the progress in your dashboard.</p>



    <p>
        <a href="{{ route('login') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Project</a>
    </p>

    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>