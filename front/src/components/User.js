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
    const [centroActual,setCentroActual] = useState("Seleccione un centro");
    const [mensajeBoton,setBotonAceptado] = useState("Cambiar rol");
    const [visible,setVisible] = useState(true);

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
            console.log ("aca")
            formData.append('centro',centrosSeleccionados[0])
            console.log(centrosSeleccionados[0]);
            formData.append('username',props.username)
            
            try {
                const response = await axios.post("http://localhost:8000/public/newVoluntario", formData,
                    {
                        headers: {
                            "Content-Type": "application/json",
                        },
                    });
                console.log('Success:', response);
                alert(`Rol cambiado con Exito`);
                setBotonAceptado("Guardar Cambios");
            } catch (error) {
                console.error('Error:', error.response.data.Mensaje);
                setMsgError(error.response.data.Mensaje);
                alert ("No se puede asignar un voluntario sin centro");
            }
        } else if (rol == "admin"){
            formData.append('username',props.username)
            
            try {
                const response = await axios.post("http://localhost:8000/public/newAdmin", formData,
                    {
                        headers: {
                            "Content-Type": "application/json",
                        },
                    });
                console.log('Success:', response);
                alert(`Rol cambiado con Exito`);
                setBotonAceptado("Guardar Cambios");
            } catch (error) {
                console.error('Error:', error.response.data.Mensaje);
                setMsgError(error.response.data.Mensaje);
                alert ("No es posible asignar administrador al usuario");
            }
        } else {
                formData.append('username',props.username)
                //formData.append('rol', "user")
                
                try {
                    const response = await axios.put("http://localhost:8000/public/updateUsuario", formData,
                        {
                            headers: {
                                "Content-Type": "application/json",
                            },
                        });
                    console.log('Success:', response);
                    alert(`Rol cambiado con Exito`);
                    setBotonAceptado("Guardar Cambios");
                } catch (error) {
                    console.error('Error:', error.response.data.Mensaje);
                    setMsgError(error.response.data.Mensaje);
                    alert ("No fue posible convertir en usuario común al voluntario/administrador");
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
        setVisible(true);
        setBotonAceptado('Cambiar rol');
    }

    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await axios.get(`http://localhost:8000/public/listarCentros?id=&nombre=&direccion=&hora_abre=&hora_cierra=`);
                setCentros(procesarcen(res.data));
                setVisible(false);
                if (props.rol == "volunt"){
                    let centroActualRec = await axios.get(`http://localhost:8000/public/getCentroVolunt?voluntario=${props.username}`)
                    console.log(centroActualRec.data);
                    setCentroActual("Actual: "+centroActualRec.data.Nombre);
                    console.log(centroActual);
                    setVisible(true);
                }
                setBotonAceptado("Guardar Cambios");
                
            } catch (error) {
                console.error("MI MENSAJE");
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

    function Telefono(){
    let Tel= 'Sin Datos'
    if (props.telefono !== 0){
        Tel = props.telefono
    } 
    return Tel;
    }

    function obtenerPuntuacion(){
        try{

        }catch (error){

        }
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
                Nombre: {props.nombre}
                <br />
                Apellido: {props.apellido}
                <br />
                DNI: {props.dni} 
                <br />
                Mail: {props.mail}
                <br />
                Telefono: {Telefono()}
                <br />
                puntuacion: {obtenerPuntuacion()}
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
                    <br/><br/>
                    {rol === "volunt"&&(
                        <select id="centro" value={centrosSeleccionados} onChange={handleCentrosChange}>
                            <br/>
                            <option value="">{centroActual}</option>
                            {centros.map((centro) => (
                                <option key={centro.id} value={centro.id}>
                                    {centro.Nombre}
                                </option>
                            ))}
                        </select>

                    )}
                    {visible === true && 
                        <ButtonSubmit text={mensajeBoton} />
                    }
                </form>

                </div>
                )}
                {(msgError)&& (
                    <>
                        {msgError}
                    </>
                )}
            </fieldset>

}

export default User