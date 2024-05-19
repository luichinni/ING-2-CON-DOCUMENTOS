import {Link} from 'react-router-dom';
import '../HarryStyles/NavBar.css';


export function NavBar(){
    return <>
    <div className='navbar'>
        <div className='navbar-items'>
            <li className='button-NavBar'>
                <Link 
                    to="/"
                    className="botonNavBar"> 
                    Publicaciones
                </Link>
                {
                //<Link to={"../IniciarSesion.js"} className="botonHader"> Iniciar sesión </Link>
                }
            </li>
            <li className='button-NavBar'>
                <a href="https://caritas.org.ar/quienes-somos/" className="botonNavBar">¿Quiénes somos?</a>
                {//<a href={"https://caritas.org.ar/quienes-somos/"} className="botonHeader"> ¿Quiénes somos? </a>
                // <Link to={"../Registrarse.js"} className="botonHeader"> Regístrarse </Link>
                //<Link to={"../IniciarSesion.js"} className="botonHader"> Iniciar sesión </Link>
                }
            </li>
            <li className='button-NavBar'>
                <Link 
                    to="/IniciarSesion"
                    className="botonNavBar"> 
                    Iniciar Sesion
                </Link>
            </li>
        </div>
    </div>
    </>
}
export default NavBar