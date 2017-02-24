<?php
    date_default_timezone_set('America/Los_Angeles');
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Stylist.php";
    require_once __DIR__."/../src/Client.php";

    $app = new Silex\Application();

    $server = 'mysql:host=localhost:3306;dbname=hair_salon';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views'));

    $app['debug'] = true;

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get("/", function() use ($app) {
        $blank_form = array();
        return $app['twig']->render('home.html.twig', array('stylists' => Stylist::getAll(), 'blank_form' => $blank_form));
    });

    $app->post("/add-stylist", function() use ($app) {
        $name = $_POST['name'];
        $specialty = $_POST['specialty'];
        $blank_form = array();
        if (!$name || !$specialty) {
            array_push($blank_form, "empty");
        } else {
            $new_stylist = new Stylist($name, $specialty);
            $new_stylist->save();
        }

        return $app['twig']->render('home.html.twig', array('stylists' => Stylist::getAll(), 'blank_form' => $blank_form));
    });

    $app->patch("/stylists/{id}", function($id) use ($app) {
        $name = $_POST['name'];
        $specialty = $_POST['specialty'];
        $this_stylist->find($id);
        $this_stylist->update($name, $specialty);
        $blank_form = array();

        return $app['twig']->render('home.html.twig', array('stylists' => Stylist::getAll(), 'blank_form' => $blank_form, 'stylist' => $this_stylist));
    });

    $app->delete("/stylists/{id}", function($id) use ($app) {
        $this_stylist = Stylist::find($id);
        $this_stylist->delete();
        $blank_form = array();

        return $app['twig']->render('home.html.twig', array('stylists' => Stylist::getAll(), 'blank_form' => $blank_form));
    });

    $app->get("/stylists/{id}", function($id) use ($app) {
        $this_stylist = Stylist::find($id);
        $blank_form = array();

        return $app['twig']->render('stylist.html.twig', array('stylist' => $this_stylist, 'clients' => $this_stylist->getClients(), 'blank_form' => $blank_form));
    });

    $app->post("/add-client", function() use ($app) {
        $client_name = $_POST['name'];
        $phone_number = $_POST['phone_number'];
        $stylist_id = $_POST['stylist_id'];
        $blank_form = array();
        if (!$client_name || !$phone_number) {
            array_push($blank_form, "empty");
        } else {
            $new_client = new Client($client_name, $phone_number, $stylist_id);
            $new_client->save();
        }
        $this_stylist = Stylist::find($stylist_id);

        return $app['twig']->render('stylist.html.twig', array('stylist' => $this_stylist, 'clients' => $this_stylist->getClients(), 'blank_form' => $blank_form));
    });

    return $app;

?>
