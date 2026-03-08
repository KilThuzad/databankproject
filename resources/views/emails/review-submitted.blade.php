<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Project Review Notification</title>
</head>
<body>
    <h2>Hello {{ $project->user->firstname }},</h2>

    <p>Your research project titled:</p>

    <h3>"{{ $project->title }}"</h3>

    <p>has been reviewed.</p>

    <hr>

    <h4>Review Summary:</h4>
    <ul>
        <li><strong>Overall Score:</strong> {{ $review->overall_score }}</li>
        <li><strong>Recommendation:</strong> {{ ucfirst($review->recommendation) }}</li>
        <li><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $project->status)) }}</li>
    </ul>

    @if($review->comments)
        <p><strong>Reviewer Comments:</strong></p>
        <p>{{ $review->comments }}</p>
    @endif

    <br>
    <p>Please log in to your account to view full details.</p>

    <p>Thank you.</p>
</body>
</html>