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
        </div>
    </div>
    </>
}
export default NavBar