import "../HarryStyles/configuracion.css";
import React, { useState, useEffect } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

const Configuracion = () => {
    const [recibeNotis, setRecibe] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get(`http://localhost:8000/public/obtenerUsuario?username=${localStorage.getItem('username')}`);
                console.log(response.data[0].notificacion);
                setRecibe(response.data[0].notificacion);
            } catch (err) {
                console.error('Error fetching data', err);
            }
        };
        fetchData();
    }, []);

    const switchHandler = async (e) => {
        const formData = new FormData();
        formData.append('username', localStorage.getItem('username'));
        formData.append('setnotificacion', e.target.value);

        try {
            await axios.put("http://localhost:8000/public/updateUsuario", formData, {
                headers: {
                    "Content-Type": "application/json",
                },
            });
            setRecibe(!recibeNotis);
        } catch (error) {
            alert('No fue posible cambiar la preferencia, intente más tarde');
            console.error('Error updating notification preference', error);
        }
    };

    return (
        <div className="contenedor">
            <br/><br/><br/><br/><br/><br/><br/><br/><br/>
            <h2>Configuración de Notificaciones</h2>
            <div>
                {recibeNotis ? (
                    <button value={false} className="estiloRed" onClick={switchHandler}>
                        Desactivar notificaciones al mail
                    </button>
                ) : (
                    <button value={true} className="estiloBlue" onClick={switchHandler}>
                        Activar notificaciones al mail
                    </button>
                )}
            </div>
            <br/>
            <h2>Configuración de Perfil</h2>
            <div>
                <Link to={`/ModificarUsuario/${localStorage.getItem('username')}`}>
                    <button className="estiloEditar">Editar Datos del Perfil</button>
                </Link>
            </div>
        </div>
    );
};

export default Configuracion;
