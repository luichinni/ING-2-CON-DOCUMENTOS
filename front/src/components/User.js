import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import "../HarryStyles/Usuarios.css"
import React, {useState} from "react";
import { ButtonSubmit } from "./ButtonSubmit";

const User = (props) => {
    const handleSubmit = async (e) => {}
    const [isExpanded, setIsExpanded] = useState(false);

    const handleToggle = () => {
        setIsExpanded(!isExpanded);
      };

    return  <fieldset className="centro-fila">
                <p>
                Nombre de usuario: {props.username}
                <button onClick={handleToggle} className="toggle-button">
                     {isExpanded ? "Ocultar Detalles" : "Mostrar Detalles"}
                </button>
                </p>
                {isExpanded && (
                <div className="detallesUsuario">
                <br />
                nombre: {props.nombre}
                <br />
                apellido: {props.apellido}
                <br />
                dni: {props.dni} 
                <br />
                mail: {props.mail}
                <br />
                telefono: {props.telefono}
                <br />
                <form onSubmit={handleSubmit}>
                    <select id="Horario">
                        <option key="usuario" value="usuario">usuario</option>
                        <option key="voluntario" value="voluntario">voluntario</option>
                        <option key="administrador" value="administrador">administrador</option>
                    </select>           
                    <ButtonSubmit text="Cambiar Rol" />
                </form>
                </div>
                )}
            </fieldset>

}

export default User