<?php require("main.php"); ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title><?= $title ?></title>
</head>

<body>
    <div class="container p-5">


        <?php if ($showResult) : ?>

            <div class="jumbotron">
                <h1 class="display-4"><?= $title ?></h1>
                <?php if ($resultFound) : ?>
                    <p class="lead">Fastest time for <?= number_format($targetDistance) ?>m is <?= secondsToHumanTime($minTimeForTargetDistance) ?> and it starts at <?= $distances[$minFoundAt] ?>m from start (<?= secondsToHumanTime($minFoundAt) ?> into the activity)</p>
                <?php else : ?>
                    <p class="lead">Target distance of <?= number_format($targetDistance) ?>m is larger than total activity distance of <?= number_format($totalDistance) ?>m </p>
                <?php endif; ?>
                <a class="btn btn-primary btn-lg float-right" href="/">Back</a>
            </div>

        <?php else : ?>
            <div class="jumbotron">
                <h1 class="display-4"><?= $title ?></h1>
                <p class="lead">Upload .fit file and find the fastest time over a custom distance.</p>
                <form action="index.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="process">
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="distance">File</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="fitFile" name="fitFile">
                                    <label class="custom-file-label" for="fitFile">Choose .fit file</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="distance">Target Distance</label>
                                <input type="text" class="form-control" id="distance" name="distance" aria-describedby="distanceHelp" value="1000">
                                <small id="distanceHelp" class="form-text text-muted">Enter distance in meters</small>
                            </div>
                        </div>

                    </div>
                    <hr class="my-4">
                    <button type="submit" class="btn btn-primary btn-lg float-right">Submit</button>
                </form>
            </div>


        <?php endif; ?>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script>
        $('#fitFile').on('change', function() {
            //get the file name
            var fileName = $(this).val();

            var fakePathPresent = fileName.toLowerCase().indexOf('fakepath');

            if (fakePathPresent !== -1) {
                fileName = fileName.substring(fakePathPresent + 9);
            }

            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fileName);
        })
    </script>
</body>

</html>