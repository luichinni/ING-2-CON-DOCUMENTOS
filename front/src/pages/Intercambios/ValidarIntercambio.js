import { ButtonSubmit } from "../../components/ButtonSubmit";
import { Button } from "../../components/Button";
import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';
import '../../HarryStyles/Intercambios.css';

const ValidarIntercambio = () => {
	const navigate = useNavigate(); 
	const [comentario, setComentario] =useState('');
	const [donacion, setDonacion] =useState('');
	const [myError, setMyError] = useState(false);
	const [intercambio, setIntercambio] = useState('')

	const handleComentarioChange = (e) => setComentario(e.target.value);
	const handleIntercambioChange = (e) => setIntercambio(e.target.value);
	const handleDonacionChange = (e) => setDonacion(e.target.value);

    const handleSubmit = async (e) => {
		
        e.preventDefault();
		console.log('Submit button clicked!');
			console.log('entro'); 
			const formData = new FormData();
			formData.append('intercambio', intercambio);
			formData.append('donacion', donacion);
			formData.append('comentario', comentario);

			try {
				setMyError(false);
				console.log('myErr  =false')
				const response = await axios.post("http://localhost:8000/public/newUsuario", formData,
					{
						headers: {
							"Content-Type": "application/json",
						},
					});
				console.log('Success:', response);
				navigate("../"); //No se donde tiene que ir 
			} catch (error) {
				console.error('Error:', error.response.data.Mensaje);
				setMyError(true);
			}
    };

    return (
	<>
	<div id="validarIntercambio">
		<br/>
		<form onSubmit={handleSubmit}>
			<h3> Valida el intercambio! </h3>  <br /> <br />			
            <label>
				<br/>
                El intercambio fue: 
				<br/><br/>
				<Button text="Confirmado" value="{intercambio}" onChange={handleIntercambioChange} /><br/><br/>
				<Button text="Cancelado" value="{intercambio}" onChange={handleIntercambioChange}/><br/><br/>
				<Button text="Rechazado" value="{intercambio}" onChange={handleIntercambioChange}/><br/><br/>
				<br/><br/><br/>
                ¿Se obtuvo alguna donación?
				<br/><br/>
				<Button text="Si" onChange={handleDonacionChange} />
				<Button text="No"onChange={handleDonacionChange}  />
            </label>

			<textarea value={comentario} onChange={handleComentarioChange} maxLength="255" placeholder="Comentario del intercambio" required></textarea>
            <br /><br />

			<ButtonSubmit text="Subir la validación" />
		</form>
				{//myError &&
					//<p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
				}
	</div>
	</>
	
	);
};

export default ValidarIntercambio;