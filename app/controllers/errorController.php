<?php

<<<<<<< HEAD
/** Shows app/views/errors/error.php. Called by the router and the exception handler. */
=======
>>>>>>> 51d018d63b7c2545946a8a2512896a281df79c85
class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->abort(404, 'The page you are looking for does not exist.');
    }

    public function forbidden(): void
    {
        $this->abort(403, 'You do not have permission to do that.');
    }

    public function methodNotAllowed(): void
    {
        $this->abort(405, 'That request method is not allowed for this page.');
    }

    public function serverError(): void
    {
        $this->abort(500, 'Something went wrong on our side. Please try again later.');
    }
<<<<<<< HEAD
}
=======
}



>>>>>>> 51d018d63b7c2545946a8a2512896a281df79c85
