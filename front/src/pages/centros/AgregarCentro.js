import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';

const AgregarCentro = () => {
	const navigate = useNavigate();
    const [nombre, setNombre] = useState('');
	const [direccion, setDireccion] = useState('');
	const [hora_abre,setHora_abre] = useState('');
	const [hora_cierra,setHora_cierra] = useState('');
	const [myError, setMyError] = useState(false);
	const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');


	const [intercambio,setIntercambio] = useState('');
	const handleIntercambioChange = (e) => setIntercambio(e.target.value);

    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleDireccionChange = (e) => setDireccion(e.target.value);
	const handleHora_AbreChange = (e) => setHora_abre (e.target.value);
	const handleHora_CierraChange = (e) => setHora_cierra (e.target.value);

    const handleSubmit = async (e) => {
        e.preventDefault();
		console.log('Submit button clicked!');

        const formData = new FormData();
        formData.append('nombre', nombre);
		formData.append('direccion', direccion);
		formData.append('hora_abre',hora_abre)
		formData.append('hora_cierra',hora_cierra)

        try {
            const response = await axios.post("http://localhost:8000/public/newCentro", formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
            console.log('Success:', response);
			navigate ("../Centros");
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
			setMyError(true);
			setMsgError(error.response.data.Mensaje);
        }
    };

    return (
		<div>
			<br /><br /><br /><br /><br /><br />
			<form onSubmit={handleSubmit}>
				<label>
					Ingrese el nombre del centro a agregar: <br/>
					<input type="text" value={nombre} onChange={handleNombreChange} required />
				</label>
				<br />
				<label>
					Ingrese la dirección del centro: <br/>
					<input type="text" value={direccion} onChange={handleDireccionChange} required />
				</label>
				<br />
                <label>
					Ingrese el horario de apertura del centro: <br/>
					<input type="text" value={hora_abre} onChange={handleHora_AbreChange} required />
				</label>
				<br />
                <label>
					Ingrese el horario de cierre del centro: <br/>
					<input type="text" value={hora_cierra} onChange={handleHora_CierraChange} required />
				</label>
				<br />
				
				<ButtonSubmit text="Agregar Centro!" />
			</form>
			{myError &&
				<p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
			}
		</div>
    );
};

export default AgregarCentro;