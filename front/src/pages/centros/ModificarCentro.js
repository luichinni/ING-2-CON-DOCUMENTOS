import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState, useEffect } from 'react';
import { useNavigate, useParams} from "react-router-dom";
import axios from 'axios';
import "../../HarryStyles/Intercambios.css"
import "../../HarryStyles/estilos.css";

const ModificarCentro = (props) => {
    const navigate = useNavigate();
    const [nombre, setNombre] = useState('');
	const [direccion, setDireccion] = useState('');
	const [hora_abre,setHora_abre] = useState('');
	const [hora_cierra,setHora_cierra] = useState('');
    const [centros, setCentros] =useState([])
    const id = useParams();
	const [myError, setMyError] = useState(false);
	const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');
    const [huboCambio, setHuboCambio] = useState(false)
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleNombreChange = (e) => {setNombre(e.target.value); setHuboCambio(true);}
    const handleDireccionChange = (e) => {setDireccion(e.target.value); setHuboCambio(true);}
	const handleHora_AbreChange = (e) => {setHora_abre (e.target.value); setHuboCambio(true);}
	const handleHora_CierraChange = (e) => {setHora_cierra (e.target.value); setHuboCambio(true);}
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData();
			formData.append('id', id);
            (nombre)&&(formData.append('setnombre', nombre));
			(direccion)&&formData.append('setDireccion', direccion);
			(hora_abre)&&formData.append('serHora_abre', hora_abre);
            (hora_cierra)&&formData.append('serHora_cierre', hora_cierra);

			try {
				setMyError(false);
                console.log(`nombre: ${formData.get('setnombre')}`);
                console.log(`direccion: ${formData.get('setdireccion')}`);
                console.log(`hora_abre: ${formData.get('sethora_abre')}`);
                console.log(`hora_cierra: ${formData.get('sethora_cierra')}`);
                if (huboCambio === true) {
                if (window.confirm('¿Seguro que deseas modificar los datos?')) {
                    const response = await axios.put("http://localhost:8000/public/updateUsuario", formData,
                    {headers: { "Content-Type": "application/json", }, });
                    console.log('Success:', response);
                    navigate("/");
                    }
                } else {
                alert('No se realizo ningun cambio')
                navigate("/");
                }
                } catch (error) {
                    console.log('entre por error')
                    console.error('Error:', error.response.data.Mensaje);
                    setMyError(true);
                    setMsgError(error.response.data.Mensaje);
                }
        
    };

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            setError('');
    
            try {
            const url = `http://localhost:8000/public/listarCentros${id}`;
            const response = await axios.get(url);
    
            if (response.data.length === 0) {
              setError('No hay centros disponibles');
              setCentros([]); 
            } else {
                const centroData = procesar(response.data)[0];
                setCentros([centroData]);
                setNombre(centroData.nombre);
                setDireccion(centroData.direccion);
                setHora_abre(centroData.hora_abre);
                setHora_cierra(centroData.hora_cierra);
            }
          } catch (error) {
            setError('Ocurrió un error al obtener el centro.');
            console.error(error);
          } finally {
            setLoading(false);
          }
        };
    
        fetchData();
    }, []);

    function procesar(centros) {
        let centroCopy = [];
        Object.keys(centros).forEach(function (clave) {
          if (!isNaN(clave)) {
            centroCopy[clave] = centros[clave]
          }
        })
        return centroCopy
      }

    return (
        <div>
            <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
            <form onSubmit={handleSubmit}>
                <label> Modificar Centro! No es necesario completar los datos no se necesitan modificar!</label>
                <label>
					Ingrese el  nuevo nombre del centro: <br/>
					<input type="text" value={nombre} onChange={handleNombreChange} required />
				</label>
				<br />
				<label>
					Ingrese la nueva dirección del centro: <br/>
					<input type="text" value={direccion} onChange={handleDireccionChange} required />
				</label>
				<br />
                <label>
					Ingrese el nuevo horario de apertura del centro: <br/>
					<input type="text" value={hora_abre} onChange={handleHora_AbreChange} required />
				</label>
				<br />
                <label>
					Ingrese el nuevo horario de cierre del centro: <br/>
					<input type="text" value={hora_cierra} onChange={handleHora_CierraChange} required />
				</label>
				<br />

                <ButtonSubmit text="Modificar centro!" /> 
            </form>
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
        </div>
    );
};

export default ModificarCentro;
