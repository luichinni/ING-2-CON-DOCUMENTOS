import {Link} from 'react-router-dom';


export function Header(){
    return <>
    <div className='header'>
        <a href={"https://caritas.org.ar/quienes-somos/"} className="botonHeader"> ¿Quiénes somos? </a>
        {
          // <Link to={"../Registrarse.js"} className="botonHeader"> Regístrarse </Link>
        //<Link to={"../IniciarSesion.js"} className="botonHader"> Iniciar sesión </Link>
}
        </div>
    </>
}
