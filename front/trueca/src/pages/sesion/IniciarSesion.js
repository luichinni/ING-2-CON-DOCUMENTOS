import {Link} from 'react-router-dom'
import { ButtonSubmit } from "../../components/ButtonSubmit";

const IniciarSesion = () => {
    return <>
    <h2>Inicio de Sesión</h2>
            <fieldset id="FormInicio">
                <form action="/login" method="post">
                    <br/>
                    <br/>
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
                    <Link 
                        to="/Registrarse"
                        className="boton"> 
                        Regístrate 
                    </Link>
                </div>
            </fieldset>
    </>
}
export default IniciarSesion;