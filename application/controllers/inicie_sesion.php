<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicie_Sesion extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('model_inicio');
		$this->load->model('model_pedidos');
		$this->load->model('model_mostrar_productos');
	}
	public function index()
	{
		//header
		$this->load->view('view_iniciesesion');
	}
	
	public function recarga_inicio(){
		$this->load->view('inicio');
	}
	public function modificar()
	{
		$id=$this->input->post('id');
		$act = $this->model_inicio->modificar($id);
			$datos = array(
				'id' => $act->id,
				'correo' =>$act->correo,
				'usuario' => $act->user,
				'genero' => $act->genero,
				'fecha_nacimiento' => $act->fecha_nacimiento,
				'contrasena' => $act->pass,
				'nombre_rol' => $act->nombre_rol,
				'id_rol' => $act->id_rol,
			);
		$this->load->view('view_editar_registro',$datos);
	}
	public function modificar_registro(){
		$id = $this->input->post("id");
		$usuario = $this->input->post("usuario");
		$correo = $this->input->post("correo");
		$genero = $this->input->post("genero");
		$fecha = $this->input->post("fecha");
		$contrasena = $this->input->post("contrasena");
		$rol = $this->input->post("rol");
		$mof = $this->model_inicio->modificar_registro($id,$usuario,$correo,$contrasena,$rol,$genero,$fecha);
	}
	public function eliminar_registro(){
		$id=$this->input->post("id");
		$eli = $this->model_inicio->eliminar_registro($id);
	}
	public function carga_admin()
	{
		if ($this->session->userdata('is_logged_in')) {
			$this->load->view('VistasAdmin/View_Inicio');
			//$this->load->view('view_admin',$data);
		}else{	
			echo "Sesion Caducada por favor ingrese nuevamente";
		}
	}
	public function carga_mesero()
	{
		if ($this->session->userdata('is_logged_in')) {
			$datos = array(
				'zonas' => $this->model_pedidos->Mostrarzonas(),
				'mesas' => $this->model_pedidos->Mostrarmesas(),
				'pedidos' => $this->model_pedidos->MostrarPedidosAFacturar()
			);
			$this->load->view('view_mesero',$datos);
		}else{	
			echo "Sesion Caducada por favor ingrese nuevamente";
		}
	}
	public function carga_facturador()
	{
		if ($this->session->userdata('is_logged_in')) {
			$data = array(
				'pedidos' => $this->model_pedidos->MostrarPedidosAFacturar()
			);
			$this->load->view('view_facturador',$data);
		}else{	
			echo "Sesion Caducada por favor ingrese nuevamente";
		}
	}
	public function olvidar_contra()
	{
		$this->load->view('view_olvidar');
	}
	public function cambia_contra()
	{
		$usuario=$this->input->post('usuario');
		$nueva_con=$this->input->post('nueva_con');
		$con_contra=$this->input->post('con_contra');
		if ($nueva_con == $con_contra) {
			$re = $this->model_inicio->cambia_con($usuario);
			if($re->cuenta == 1){
				$result = $this->model_inicio->act_usuario($usuario,$nueva_con);
			}
		}
	}

	public function inicio(){
		$usuario=$this->input->post('usuario');
		$contrasena=$this->input->post('contrasena');

		$re = $this->model_inicio->inicio($usuario,$contrasena);
		if($re->cuenta == 1){
			$result = $this->model_inicio->con_usuario($usuario,$contrasena);
			//echo "correcto";
			$session = array(
				'ID' => $result->id,
				'USUARIO' => $result->user,
				'CONTRASENA' => $result->pass,
				'ROL' => $result->nombre_rol,
				'is_logged_in' => TRUE,
			);
			$this->session->set_userdata($session);
			if ($result->nombre_rol == "sinAsignar") {
					echo "'<b>'Espera que el Administrador le asigne un Servicio'</b>''";
			}
			elseif ($result->nombre_rol == "admin") {
				if ($this->session->userdata('is_logged_in')) {
					redirect("".base_url()."index.php/inicie_sesion/carga_admin");
				}
			}elseif ($result->nombre_rol == "mesero") {
				if ($this->session->userdata('is_logged_in')) {
					redirect("".base_url()."index.php/inicie_sesion/carga_mesero");
				}
			}elseif ($result->nombre_rol == "facturador") {
				if ($this->session->userdata('is_logged_in')) {
					redirect("".base_url()."index.php/inicie_sesion/carga_facturador");
				}
			}
		}else{
			echo "Contrasena o usuario incorrecto, Intentelo de nuevo o Cambie su contrasena";
			$this->load->view('view_iniciesesion');
		}
	}
}