import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';
import '../../HarryStyles/Intercambios.css';

const ValidarIntercambio = () => {
	const navigate = useNavigate(); 
    const [nombre, setNombre] = useState('');
    const [apellido, setApellido] = useState('');
    const [edad, setEdad] = useState('');
    const [numeroDocumento, setNumeroDocumento] = useState('');
    const [mail, setEmail] = useState('');
    const [telefono, setTelefono] = useState('');
	const [comentario, setComentario] =useState('');
	const [donacion, setDonacion] =useState('');
	const [myError, setMyError] = useState(false);
	const [intercambio, setIntercambio] = useState('')

    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleApellidoChange = (e) => setApellido(e.target.value);
    const handleEdadChange = (e) => setEdad(e.target.value);
    const handleNumeroDocumentoChange = (e) => setNumeroDocumento(e.target.value);
    const handleMailChange = (e) => setEmail(e.target.value);
    const handleTelefonoChange = (e) => setTelefono(e.target.value);
	const handleComentarioChange = (e) => setComentario(e.target.value);
	const handleIntercambioChange = (e) => setIntercambio(e.target.value);
	const handleDonacionChange = (e) => setDonacion(e.target.value);

    const handleSubmit = async (e) => {
		
        e.preventDefault();
		console.log('Submit button clicked!');

		if (edad < 18){
			setMyError(true);
		}else{
			console.log('entro');
			const formData = new FormData();
			formData.append('nombre', nombre);
			formData.append('apellido', apellido);
			formData.append('edad', edad);
			formData.append('dni', numeroDocumento);
			formData.append('mail', mail);
			formData.append('telefono', telefono);
			formData.append('rol', "user");

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
		}
    };

    return (
	<>
	<br/><br/><br/><br/><br/>
	<h1>Trueca </h1>
	<div id="validarIntercambio">
		<br/>
		<form onSubmit={handleSubmit}>
			<h3> Valida el intercambio para poder hacer estadisticas del centro! </h3>  <br /> <br />
			
            <label>
                ¿Se realizó el intercambio?
                        <input className="Seleccion" type="radio" value="sí" checked={intercambio === 'sí'} onChange={handleIntercambioChange} /> Sí
                        <input className="Seleccion" type="radio" value="no" checked={intercambio === 'no'} onChange={handleIntercambioChange} />No 
            </label>


            <label>
                ¿Se obtuvo alguna donación?
                <div>
                    <label>
                        <input type="radio" value="sí" checked={donacion === 'sí'} onChange={handleDonacionChange} />
                        Sí
                    </label>
                    <label>
                        <input type="radio" value="no" checked={donacion === 'no'} onChange={handleDonacionChange} />
                        No
                    </label>
                </div>
            </label>

			<input placeholder='Nombre' type="text" value={nombre} onChange={handleNombreChange} required /> <br/><br/>  


			<input placeholder='Apellido' type="text" value={apellido} onChange={handleApellidoChange} required />  <br/><br/>  


			<input placeholder='Edad' type="text" value={edad} onChange={handleEdadChange} required />  <br/><br/>  


			<input placeholder='N° de DNI' type="text" value={numeroDocumento} onChange={handleNumeroDocumentoChange} required />  <br/><br/>  

	
			<input placeholder='Email' type="text"  value={mail} onChange={handleMailChange} required /> <br/> <br/> 
			

			<input placeholder='Telefono' type="text" value={telefono} onChange={handleTelefonoChange} />  <br/><br/>  
			
			<ButtonSubmit text="Registrarse" />
		</form>
				{myError &&
					<p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>error</p>
				}
	</div>
	</>
	
	);
};

export default ValidarIntercambio;