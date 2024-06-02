import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import "../HarryStyles/Usuarios.css"
import React, {useState} from "react";
import { ButtonSubmit } from "./ButtonSubmit";
import axios from "axios";


const User = (props) => {
    const [isExpanded, setIsExpanded] = useState(false);
    const [rol, setRol] = useState(props.rol);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');
    const roles = ["user", "volunt", "admin"]

    const handleSubmit = async (e) => {

        e.preventDefault();
		console.log('apretaste el boton de cambiar rol');

		console.log('entro');
		const formData = new FormData();
		formData.append('setrol',rol);
        formData.append('username',props.username)

		try {
			const response = await axios.put("http://localhost:8000/public/updateUsuario", formData,
				{
					headers: {
						"Content-Type": "application/json",
					},
				});
			console.log('Success:', response);
            alert(`Rol cambiado con Exito`);
		} catch (error) {
			console.error('Error:', error.response.data.Mensaje);
			setMsgError(error.response.data.Mensaje);
		}
    };
    const handleToggle = () => {
        setIsExpanded(!isExpanded);
      };
    
    function convertirNombre(rol){
        if (rol == "user"){
            return "usuario"
        }else if (rol == "volunt"){
            return "voluntario"
        }else{
            return "administrador"
        }
        return "no tiene rol";
    }

    function setear (e) {
        setRol(e.target.value)
    }


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
                    <select id="rol" value={rol} onChange={(e) => setear(e)}>
                        <option key="actual" value={props.rol} disabled>
                            {convertirNombre(props.rol)}
                        </option>
                        {roles.filter(role => role !== props.rol).map(role => (
                        <option key={role} value={role}>{convertirNombre(role)}</option>
                        ))}
                    </select>
                    <ButtonSubmit text="Cambiar Rol" />
                </form>
                </div>
                )}
            </fieldset>

}

export default User