<!DOCTYPE html>
<html>
<head>
    <title>Payment Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="min-width: 300px;">
        <h3 class="mb-4 text-center">Payment Test</h3>
        <div class="d-flex flex-column gap-3">
            <form action="" method="POST">
                @csrf
                <button type="submit" class="btn btn-success w-100">Payment Success</button>
            </form>
            <form action="" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Payment Failed</button>
            </form>
        </div>
    </div>
</body>
</html>
