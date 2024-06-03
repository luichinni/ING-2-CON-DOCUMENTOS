import "../HarryStyles/centros.css"
import "../HarryStyles/styles.css"
import "../HarryStyles/Usuarios.css"
import React, {useState, useEffect} from "react";
import { ButtonSubmit } from "./ButtonSubmit";
import axios from "axios";


const User = (props) => {
    const [isExpanded, setIsExpanded] = useState(false);
    const [rol, setRol] = useState(props.rol);
    const [centros, setCentros] = useState([]);
    const [centrosSeleccionados, setCentrosSeleccionados] = useState([]);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');
    const roles = ["user", "volunt", "admin"]


    const handleCentrosChange = (e) => {
        const selectedValues = Array.from(e.target.selectedOptions, option => option.value);
        setCentrosSeleccionados(selectedValues);
    };

    const handleSubmit = async (e) => {

        e.preventDefault();
		console.log('apretaste el boton de cambiar rol');

		console.log('entro');
		const formData = new FormData();
		formData.append('setrol',rol);

        if (rol == "volunt"){
            formData.append('setCentro',centrosSeleccionados)
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
                alert (msgError);
            }
        } else if (rol == "volunt"){
            formData.append('setCentro',centrosSeleccionados)
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
                alert (msgError);
            }
        } else {
                formData.append('setCentro',centrosSeleccionados)
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
                    alert (msgError);
                }
        }
    };
    const handleToggle = () => {
        setIsExpanded(!isExpanded);
      };
    
    function convertirNombre(rol){
        if (rol === "user"){
            return "usuario"
        }else if (rol === "volunt"){
            return "voluntario"
        }else{
            return "administrador"
        }
    }

    function setear (e) {
        setRol(e.target.value)
    }

    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await axios.get(`http://localhost:8000/public/listarCentros?id=&nombre=&direccion=&hora_abre=&hora_cierra=`);
                setCentros(procesarcen(res.data));
            } catch (error) {
                console.error(error);
            }
        };
        fetchData();
    }, []);
    function procesarcen(centros) {
        let cenCopy = [];
        Object.keys(centros).forEach(function (clave) {
            if (!isNaN(clave)) {
                cenCopy[clave] = centros[clave]
            }
        })
        return cenCopy
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
                    {rol === "volunt"&&(
                        <select id="centro" value={centrosSeleccionados} onChange={handleCentrosChange}>
                            <option value="">Seleccione un centro</option>
                            {centros.map((centro) => (
                                <option key={centro.id} value={centro.id}>
                                    {centro.Nombre}
                                </option>
                            ))}
                        </select>

                    )}
                    <ButtonSubmit text="Cambiar Rol" />
                </form>

                </div>
                )}
            </fieldset>

}

export default User