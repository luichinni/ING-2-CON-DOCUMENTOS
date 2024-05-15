import {Link} from 'react-router-dom'

export function Header(){
    return <>
        <div className="bg-red-950 py-5 text-right">
        {
        //<button className="bg-customRed text-white hover:bg-red-800 px-4 py-2 mx-2 rounded-full">Productos</button> 
        // <button className="bg-customRed text-white hover:bg-red-800 px-4 py-2 mx-2 rounded-full">Categorías</button>
        }
        <Link to={"https://caritas.org.ar/"} className="boton"> ¿Quiénes somos? </Link>
        <Link to={"Registrarse.js"} className="boton"> Regístrarse </Link>
        <Link to={"IniciarSesion.js"} className="boton"> Iniciar sesión </Link>
      </div>
      <div className="text-center">
        <img 
          src={bannerImagen} 
          alt="Banner"  
          style={{ 
            width: '800px',
            height: 'auto', 
            display: 'block', 
            margin: '0 auto' 
          }} 
        />
      </div>
    </>
}
