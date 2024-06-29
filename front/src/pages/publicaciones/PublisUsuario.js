import React from 'react';
import { useParams } from 'react-router-dom';
import '../../HarryStyles/Publicaciones.css';
import '../../HarryStyles/styles.css';
import ListarPublisUsuario from './ListarPublisUsuario';
import { useState, useEffect } from "react";
import axios from "axios";

const MisPublis = () => {
    const { username } = useParams();
    const [valoraciones, setValoraciones] = useState('');
    const [error, setError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');
    const [usuarios, setUsuarios] = useState([]);
    const [loading, setLoading] = useState(false);
    const [parametros, setParametros] = useState({ username: username });

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            setError('');

            try {
                const queryParams = new URLSearchParams(parametros).toString();
                const url = `http://localhost:8000/public/listarUsuarios?${queryParams}`;
                const response = await axios.get(url);

                if (response.data.length === 0) {
                    setError('No hay usuarios disponibles');
                    setUsuarios([]);
                } else {
                    setUsuarios(procesar(response.data));
                }
            } catch (error) {
                setError('Ocurrió un error al obtener los usuarios.');
                console.error(error);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
        fetchValoraciones();
    }, [parametros]);

    const fetchValoraciones = async () => {
        setError('');
        try {
            const url = `http://localhost:8000/public/getValoracion?userValorado=${username}&token=${localStorage.getItem('token')}`;
            const response = await axios.get(url);

            if (!response.data || response.data.Valoracion === undefined) {
                setError('No hay valoraciones disponibles');
                setValoraciones('Sin valoraciones');
            } else {
                setValoraciones(response.data.Valoracion);
            }
        } catch (error) {
            setValoraciones('Sin valoraciones');
            console.error(error);
        }
    };

    function procesar(usuarios) {
        let usuarioCopy = [];
        Object.keys(usuarios).forEach(function (clave) {
            if (!isNaN(clave)) {
                usuarioCopy[clave] = usuarios[clave];
            }
        });
        return usuarioCopy;
    }

    return (
        <div className='dashboard'>
            <br /><br /><br /><br /><br /><br />
            <div className='datosDeUser'>
                <h2>Nombre de Usuario: {username}</h2>
                {usuarios.map(usuario => (
                    <p key={usuario.id}> {/* Usar un key único para cada elemento */}
                        Nombre: {usuario.nombre} <br />
                        Apellido: {usuario.apellido}
                    </p>
                ))}
                <div className="valoracion">
                    Puntuación: 
                    <span>
                        {valoraciones === 'Sin valoraciones' ? valoraciones : `${valoraciones}/5`}
                    </span>
                </div>
            </div>

            <ListarPublisUsuario />
        </div>
    );
}

export default MisPublis;
