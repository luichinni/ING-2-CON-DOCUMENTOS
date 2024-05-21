import {Link} from 'react-router-dom';
import React, { useState } from 'react';
import '../HarryStyles/NavBar.css';


export function NavBar(){

    const [menuOpen, setMenuOpen] = useState(false);

    const toggleMenu = () => {
        if (menuOpen) {
            setMenuOpen(false);
        } else {
            setMenuOpen(true);
        }
    };
    
    return <>
    <div className='navbar'>
        <div className='navbar-items'>
            <li className='button-NavBar'>
                <Link 
                    to="/"
                    className="botonNavBar"> 
                    Publicaciones
                </Link>
            </li>
            <li className='button-NavBar'>
                <Link 
                    to="/agregarPublicacion"
                    className="botonNavBar"> 
                    Subir Publicacion
                </Link>
            </li>
            <li className='button-NavBar'>
                <Link 
                    to="/agregarCentro"
                    className="botonNavBar"> 
                    Agregar Centro  
                </Link>
            </li>
           {/* <li className='button-NavBar'>
                <Link 
                    to="/AgregarCategoria"
                    className="botonNavBar"> 
                    Agregar Categoria
                </Link>
</li>*/}
            <li className='button-NavBar'>
                <Link 
                    to="/Centros"
                    className="botonNavBar"> 
                    Centros
                </Link>
            </li>
            <li className='button-NavBar'>
                <Link 
                    to="/MisPublicaciones"
                    className="botonNavBar"> 
                    Mis Publicaciones
                </Link>
            </li>
            <li className='button-NavBar'>
                <a href="https://caritas.org.ar/quienes-somos/" className="botonNavBar">¿Quiénes somos?</a>
            </li>
            <li className='button-NavBar'>
                <Link 
                    to="/IniciarSesion"
                    className="botonNavBar"> 
                    Iniciar Sesion
                </Link>
            </li>
            <li className='button-NavBar'>
                <button className="botonNavBar" onClick={toggleMenu}>Menú</button>
            </li>
            {menuOpen && (
                <div className={`dropdown-menu ${menuOpen ? 'show' : ''}`}>
                <ul>
                    <li><button onClick={() => console.log("Cerrar Sesión")}>Cerrar Sesión</button></li>
                    <li><button onClick={() => console.log("Configuraciones")}>Configuraciones</button></li>
                    <li><button onClick={() => console.log("Ver mi Perfil")}>Ver mi Perfil</button></li>
                </ul>
                </div>
            )}

          {/*  <li className='button-NavBar'>
                <button className="botonNavBar" onClick={toggleMenu}>categorias</button>
            </li>
            {menuOpen && (
                <div className={`dropdown-menu ${menuOpen ? 'show' : ''}`}>
                <ul>
                    <li><button onClick={() => console.log("Cerrar Sesión")}>Articulos</button></li>
                    <li><button onClick={() => console.log("Configuraciones")}>Configuraciones</button></li>
                    <li><button onClick={() => console.log("Ver mi Perfil")}>Ver mi Perfil</button></li>
                </ul>
                </div>
            )}*/}

        </div>
    </div>
    </>
}
export default NavBar