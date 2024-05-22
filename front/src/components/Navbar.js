import {Link} from 'react-router-dom';
import React, { useState, useEffect } from 'react';
import '../HarryStyles/NavBar.css';
import {ButtonCerrarSesion} from './ButttonCerrarSesion';


export function NavBar(){

    const [menuOpen, setMenuOpen] = useState(false);
    /* const [Token, setToken] = useState(false); */

    const toggleMenu = () => {
        if (menuOpen) {
            setMenuOpen(false);
        } else {
            setMenuOpen(true);
        }
    };

    /* setToken(localStorage.getItem('token')); */
    
    const Token = localStorage.getItem('token');

    useEffect(() => {
    },[Token]);

    return (
    <div className='navbar'>
        <div className='navbar-items'>
            <li className='button-NavBar'>
                <Link 
                    to="/"
                    className="botonNavBar"> 
                    Inicio
                </Link>
            </li>
            <li className='button-NavBar'>
                <Link 
                    to="/Explorar"
                    className="botonNavBar"> 
                    Explorar
                </Link>
            </li>
            {(Token == 'tokenUser') ?(
            <>
                <li className='button-NavBar'>
                    <Link 
                        to="/agregarPublicacion"
                        className="botonNavBar"> 
                        Subir Publicacion
                    </Link>
                </li>
                <li className='button-NavBar'>
                    <Link 
                        to="/MisPublicaciones"
                        className="botonNavBar"> 
                        Mis Publicaciones
                    </Link>
                </li>
            </>
            ):(Token == 'tokenAdmin')?(
            <>
                <li className='button-NavBar'>
                    <Link 
                        to="/Categorias"
                        className="botonNavBar"> 
                        Categoria
                    </Link>
                </li>
                <li className='button-NavBar'>
                    <Link 
                        to="/Centros"
                        className="botonNavBar"> 
                        Centros
                    </Link>
                </li> 
            </>
            ):(<></>)}
            <li className='button-NavBar'>
                <a href="https://caritas.org.ar/quienes-somos/" className="botonNavBar">¿Quiénes somos?</a>
            </li>
            {(Token == null) ? 
            (<li className='button-NavBar'>
                <Link 
                    to="/IniciarSesion"
                    className="botonNavBar"> 
                    Iniciar Sesion
                </Link>
            </li>) :
            (
            <li className='button-NavBar'>
                <button className="botonNavBar" onClick={toggleMenu}>Menú</button>
            </li>)}
            {menuOpen && (
                <div className={`dropdown-menu ${menuOpen ? 'show' : ''}`}>
                <ul>
                    <li><button onClick={() => console.log("Ver mi Perfil")}>Ver mi Perfil</button></li>
                    <li><button onClick={() => console.log("Configuraciones")}>Configuraciones</button></li>
                    <li><ButtonCerrarSesion /></li>
                </ul>
                </div>
            )}
            {/*<li className='button-NavBar'>
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
    );
}
export default NavBar