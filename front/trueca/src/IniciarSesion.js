import {Link} from 'react-router-dom'

root.render(IniciarSesion())
export function IniciarSesion(){
    return <>
    <h2>Inicio de Sesión</h2>
            <fieldset id="FormInicio">
                <form action="/login" method="post">
                    <input placeholder="Ingrese su usuario" type="text" id="completar" name="usuario" required /> 
                    <br/>
    
                    <input placeholder="Ingrese su contraseña" type="password" id="completar" name="contrasena" required />
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