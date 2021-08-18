<?php
/*
 * Dynmic_menu.php
 */
class Dynamic_menu {
    // para CodeIgniter Super Global Referencias o variables globales
    private $ci;
    private $class_parent = 'class="waves-effect active"';
    private $class_last = '';

    function __construct() {
      // get a reference to CodeIgniter.
      $this->ci =& get_instance();
    }
     /**
     * build_menu($table, $type)
     * @param    string    the MySQL database table name.
     * @param    string    the type of menu to display.
     * @return    string    $html_out using CodeIgniter achor tags.
     */
    function build_menu() {
      $menu = array();
      $controller = $this->ci->router->fetch_class();
      $method = $this->ci->router->fetch_method();
      $usuario = $this->ci->session->userdata('usuario');
      $usuarios = $this->ci->db->where('id', $usuario)->get('usuario')->row();
      $perfil = $this->ci->session->userdata('perfil');
      $perfiles = $this->ci->db->where('id', $perfil)->get('perfil')->row();
      $perfilmenus = $this->ci->db->where('perfil', $perfil)->order_by("posicion", "asc")->get('perfilmenu')->result();
      // now we will build the dynamic menus.
      $html_out  = '';
      $html_out .= '<ul class="nav navbar-nav">';
      // me despliega del query los rows de la base de datos que deseo utilizar
      foreach ($perfilmenus as $data) {
        $id = $data->id;
        $perfil = $data->perfil;
        $menu = $data->menu;
        $parent_id = $data->parent_id;
        $is_parent = $data->is_parent;
        $menus = $this->ci->db->where('id', $menu)->get('menu')->row();
        $title = $menus->nombre;
        $url = $menus->url;
        $icon = $menus->icono;
        // are we allowed to see this menu?
        if ($parent_id == 0) {
          if ($is_parent == TRUE) {
            // CodeIgniter's anchor(uri segments, text, attributes) tag.
            $html_out .= '<li class="dropdown">';
            $html_out .= '<a href="#" class="dropdown-toggle flat-box" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
            $html_out .= '<i class="'.$icon.'"></i><span class="menu-text">'.$title.'</span><span class="caret"></span>';
            $html_out .= $this->get_childs($id);
            $html_out .= '</li>';
          } else {
            $html_out .= '<li class="flat-box">'.anchor($url, '<i class="'.$icon.'"></i><span class="menu-text">'.$title.'</span>', 'name="'.$title.'" id="'.$id.'"').'</li>';
          }
        }
        // print_r($id);
      }
      // loop through and build all the child submenus.
      $html_out .= '</ul>';
      return $html_out;
    }
     /**
     * get_childs($menu, $parent_id) - SEE Above Method.
     * Description:
     * Builds all child submenus using a recurse method call.
     * @param    mixed    $id
     * @param    string    $id usuario
     * @return    mixed    $html_out if has subcats else FALSE
     */
    function get_childs($id) {
        $has_subcats = FALSE;
        $controller = $this->ci->router->fetch_class();
        $method = $this->ci->router->fetch_method();
        $perfil = $this->ci->session->userdata('perfil');
        $html_out  = '';
        // query q me ejecuta el submenu filtrando por usuario y para buscar el submenu segun el id que traigo
        $perfilmenus = $this->ci->db->where('perfil', $perfil)->where('parent_id', $id)->order_by("posicion", "asc")->get('perfilmenu')->result();
        $html_out .= '<ul class="dropdown-menu">';
         foreach ($perfilmenus as $data) {
           $id = $data->id;
           $perfil = $data->perfil;
           $menu = $data->menu;
           $parent_id = $data->parent_id;
           $is_parent = $data->is_parent;
           $menus = $this->ci->db->where('id', $menu)->get('menu')->row();
           $title = $menus->nombre;
           $url = $menus->url;
           $icon = $menus->icono;
           $has_subcats = TRUE;
           $html_out .= '<li class="flat-box">'.anchor($url, '<span class="menu-text">'.$title.'</span>', 'name="'.$title.'" id="'.$id.'"').'</li>';
        }
        $html_out .= '</ul>';
        return ($has_subcats) ? $html_out : FALSE;
    }

    function get_childs_sidebar($id, $title) {
      $has_subcats = FALSE;
      $controller = $this->ci->router->fetch_class();
      $method = $this->ci->router->fetch_method();
      $perfil = $this->ci->session->userdata('perfil');
      $html_out  = '';
      // query q me ejecuta el submenu filtrando por usuario y para buscar el submenu segun el id que traigo
      $perfilmenus = $this->ci->db->where('perfil', $perfil)->where('parent_id', $id)->order_by("posicion", "asc")->get('perfilmenu')->result();
      $html_out .= '<ul class="collapse list-unstyled" id="'.$title.'">';
       foreach ($perfilmenus as $data) {
         $id = $data->id;
         $perfil = $data->perfil;
         $menu = $data->menu;
         $parent_id = $data->parent_id;
         $is_parent = $data->is_parent;
         $menus = $this->ci->db->where('id', $menu)->get('menu')->row();
         $title = $menus->nombre;
         $url = $menus->url;
         $icon = $menus->icono;
         $has_subcats = TRUE;
         $html_out .= '<li class="flat-box">'.anchor($url, '<span class="menu-text">'.$title.'</span>', 'name="'.$title.'" id="'.$id.'"').'</li>';
      }
      $html_out .= '</ul>';
      return ($has_subcats) ? $html_out : FALSE;
  }
    function build_menu_sidebar() {
      $menu = array();
      $controller = $this->ci->router->fetch_class();
      $method = $this->ci->router->fetch_method();
      $usuario = $this->ci->session->userdata('usuario');
      $usuarios = $this->ci->db->where('id', $usuario)->get('usuario')->row();
      $perfil = $this->ci->session->userdata('perfil');
      $perfiles = $this->ci->db->where('id', $perfil)->get('perfil')->row();
      $perfilmenus = $this->ci->db->where('perfil', $perfil)->order_by("posicion", "asc")->get('perfilmenu')->result();
      // now we will build the dynamic menus.
      $html_out  = '';
      $html_out .= '<ul class="list-unstyled components">';
      // me despliega del query los rows de la base de datos que deseo utilizar
      foreach ($perfilmenus as $data) {
        $id = $data->id;
        $perfil = $data->perfil;
        $menu = $data->menu;
        $parent_id = $data->parent_id;
        $is_parent = $data->is_parent;
        $menus = $this->ci->db->where('id', $menu)->get('menu')->row();
        $title = $menus->nombre;
        $url = $menus->url;
        $icon = $menus->icono;
        // are we allowed to see this menu?
        if ($parent_id == 0) {
          if ($is_parent == TRUE) {
            // CodeIgniter's anchor(uri segments, text, attributes) tag.
            $html_out .= '<li class="">';
            $html_out .= '<a href="#'.$title.'" data-toggle="collapse" aria-expanded="false">';
            $html_out .= '<i class="'.$icon.'"></i><span class="menu-text">'.$title.'</span><span class="caret"></span>';
            $html_out .= $this->get_childs_sidebar($id, $title);
            $html_out .= '</li>';
          } else {
            $html_out .= '<li class="flat-box">'.anchor($url, '<i class="'.$icon.'"></i><span class="menu-text">'.$title.'</span>', 'name="'.$title.'" id="'.$id.'"').'</li>';
          }
        }
        // print_r($id);
      }
      // loop through and build all the child submenus.
      $html_out .= '</ul>';
      return $html_out;
    }

}
