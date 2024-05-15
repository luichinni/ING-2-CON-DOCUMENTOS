import {Link} from 'react-router-dom'

root.render(IniciarSesion())
export function IniciarSesion(){
    return <>
    <h2>Inicio de Sesión</h2>
            <fieldset id="FormInicio">
                <form action="/login" method="post">
                    <label id="formtext" >Usuario:</label>
                    <input type="text" id="completar" name="usuario" required /> 
                    <br/>
    
                    <label id="formtext" for="contrasena">Contraseña:</label>
                    <input type="password" id="completar" name="contrasena" required />
                    <br/>
    
                    <ButtonSubmit text="Iniciar sesión"/>
                </form>
            </fieldset>
            <br />
            <fieldset>
                <div>
                    <label>¿No tienes una cuenta? </label>
                    <Link to={"registrarse.js"} className="boton"> Regístrate </Link>
                </div>
            </fieldset>
    </>
}