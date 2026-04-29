<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Inventario;
use App\Models\Unidad;

class InventarioController extends Controller {
    
    public function index($unidad_id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo', 'Responsable de Unidad', 'Asistente de Unidad']);
        $user = Auth::user();

        // RBAC: Si no es un rol de gestión global, validar unidad
        $globalRoles = ['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo'];
        if (!in_array($user['rol'], $globalRoles)) {
            if ($user['unidad_id'] != $unidad_id) {
                $this->redirect('/dashboard');
            }
        }

        $invModel = new Inventario();
        $items = $invModel->getByUnidad($unidad_id);
        
        $unidadModel = new Unidad();
        $unidad = $unidadModel->findById($unidad_id);

        $this->view('inventario/index', [
            'title' => 'Inventario - ' . $unidad['nombre'],
            'unidad' => $unidad,
            'items' => $items,
            'user' => $user
        ]);
    }

    public function grupo() {
        Auth::requireRole(['Superusuario']);
        $user = Auth::user();

        $invModel = new Inventario();
        $groupItems = $invModel->getGroupAssets();
        $globalItems = $invModel->getGlobal();

        $this->view('grupo/inventario', [
            'title' => 'Inventario General de Grupo',
            'groupItems' => $groupItems,
            'globalItems' => $globalItems,
            'user' => $user
        ]);
    }

    public function guardar($unidad_id = null) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invModel = new Inventario();
            $data = [
                'unidad_id' => !empty($_POST['unidad_id']) ? $_POST['unidad_id'] : $unidad_id,
                'nombre_item' => $_POST['nombre_item'],
                'categoria' => $_POST['categoria'],
                'cantidad' => $_POST['cantidad'],
                'estado' => $_POST['estado'],
                'observaciones' => $_POST['observaciones']
            ];

            if (!empty($_POST['id'])) {
                $invModel->update($_POST['id'], $data);
            } else {
                $invModel->create($data);
            }

            if ($unidad_id) {
                $this->redirect("/unidades/$unidad_id/inventario");
            } else {
                $this->redirect("/grupo/inventario");
            }
        }
    }

    public function eliminar($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Unidad', 'Asistente de Unidad']);
        $invModel = new Inventario();
        $invModel->delete($id);
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/dashboard');
    }
}
