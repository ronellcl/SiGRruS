<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        if (isset($_GET['switch_env'])) {
            unset($_SESSION['sigrrus_env']);
        }
        
        // Si ya hay un ambiente elegido, ir al login (o dashboard si está logueado)
        if (isset($_SESSION['sigrrus_env'])) {
            $this->redirect('/auth/login');
        }

        $this->view('home/index', [
            'title' => 'SiGRruS - Gestión de Guías y Scouts'
        ]);
    }

    public function setEnv($env) {
        if (in_array($env, ['production', 'testing'])) {
            $_SESSION['sigrrus_env'] = $env;
        }
        $this->redirect('/auth/login');
    }
}
