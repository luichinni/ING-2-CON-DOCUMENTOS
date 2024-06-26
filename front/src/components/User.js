import "../HarryStyles/centros.css";
import "../HarryStyles/styles.css";
import "../HarryStyles/Usuarios.css";
import React, { useState, useEffect } from "react";
import { ButtonSubmit } from "./ButtonSubmit";
import axios from "axios";

const User = (props) => {
    const [isExpanded, setIsExpanded] = useState(false);
    const [rol, setRol] = useState(props.rol);
    const [centros, setCentros] = useState([]);
    const [centrosSeleccionados, setCentrosSeleccionados] = useState([]);
    const [error, setError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');
    const roles = ["user", "volunt", "admin"];
    const [centroActual, setCentroActual] = useState("Seleccione un centro");
    const [mensajeBoton, setBotonAceptado] = useState("Cambiar rol");
    const [visible, setVisible] = useState(true);
    const [valoraciones, setValoraciones] = useState('');

    const handleCentrosChange = (e) => {
        const selectedValues = Array.from(e.target.selectedOptions, option => option.value);
        setCentrosSeleccionados(selectedValues);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('setrol', rol);
        formData.append('username', props.username);

        try {
            if (rol === "volunt") {
                if (centrosSeleccionados.length === 0) {
                    alert("No se puede asignar un voluntario sin centro");
                    return;
                }
                formData.append('centro', centrosSeleccionados[0]);
                await axios.post("http://localhost:8000/public/newVoluntario", formData, {
                    headers: { "Content-Type": "application/json" },
                });
            } else if (rol === "admin") {
                await axios.post("http://localhost:8000/public/newAdmin", formData, {
                    headers: { "Content-Type": "application/json" },
                });
            } else {
                await axios.put("http://localhost:8000/public/updateUsuario", formData, {
                    headers: { "Content-Type": "application/json" },
                });
            }
            alert(`Rol cambiado con éxito`);
            setBotonAceptado("Guardar Cambios");
        } catch (error) {
            console.error('Error:', error.response.data.Mensaje);
            setMsgError(error.response.data.Mensaje);
            alert(error.response.data.Mensaje || "Ocurrió un error");
        }
    };

    const fetchValoraciones = async () => {
        setError('');
        try {
            const url = `http://localhost:8000/public/getValoracion?userValorado=${props.username}&token=${localStorage.getItem('token')}`;
            console.log(`llegue, url: ${url}`)
            const response = await axios.get(url);
            console.log(`llegue2, response:${response.data}`)

            if (!response.data || response.data.valoracion === undefined) {
                setError('No hay valoraciones disponibles');
                setValoraciones('Sin valoraciones');
                console.log(`entre por error de undefined`)
            } else {
                setValoraciones(response.data.valoracion);
                console.log(`entre a gurdar datos`)
            }
        } catch (error) {
            setError('No hay valoraciones disponibles.');
            setValoraciones('Sin valoraciones');
            console.error(error);
            console.log(`entre por error`)
        }
    };

    const handleToggle = () => {
        setIsExpanded(!isExpanded);
        if (!isExpanded) {
            fetchValoraciones();
        }
    };

    const convertirNombre = (rol) => {
        switch (rol) {
            case "user":
                return "usuario";
            case "volunt":
                return "voluntario";
            case "admin":
                return "administrador";
            default:
                return "";
        }
    };

    const setear = (e) => {
        setRol(e.target.value);
        setVisible(true);
        setBotonAceptado('Cambiar rol');
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const res = await axios.get(`http://localhost:8000/public/listarCentros?id=&nombre=&direccion=&hora_abre=&hora_cierra=`);
                setCentros(procesarCen(res.data));
                setVisible(false);

                if (props.rol === "volunt") {
                    const centroActualRec = await axios.get(`http://localhost:8000/public/getCentroVolunt?voluntario=${props.username}`);
                    setCentroActual("Actual: " + centroActualRec.data.Nombre);
                    setVisible(true);
                }
                setBotonAceptado("Guardar Cambios");
            } catch (error) {
                console.error("Error fetching data", error);
            }
        };
        fetchData();
    }, [props.rol, props.username]);

    const procesarCen = (centros) => {
        return Object.values(centros).filter((centro) => !isNaN(centro.id));
    };

    const Telefono = () => {
        return props.telefono !== 0 ? props.telefono : 'Sin Datos';
    };

    return (
        <fieldset className="centro-fila">
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
                    Puntuación: {valoraciones}/5
                    <br />
                    <form onSubmit={handleSubmit}>
                        <select id="rol" value={rol} onChange={setear}>
                            <option key="actual" value={props.rol} disabled>
                                {convertirNombre(props.rol)}
                            </option>
                            {roles.filter(role => role !== props.rol).map(role => (
                                <option key={role} value={role}>{convertirNombre(role)}</option>
                            ))}
                        </select>
                        <br /><br />
                        {rol === "volunt" && (
                            <select id="centro" value={centrosSeleccionados} onChange={handleCentrosChange}>
                                <option value="">{centroActual}</option>
                                {centros.map((centro) => (
                                    <option key={centro.id} value={centro.id}>
                                        {centro.Nombre}
                                    </option>
                                ))}
                            </select>
                        )}
                        {visible && <ButtonSubmit text={mensajeBoton} />}
                    </form>
                </div>
            )}
            {error && (
                <p>{msgError}</p>
            )}
        </fieldset>
    );
};

export default User;
