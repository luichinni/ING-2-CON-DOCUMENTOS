import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';

const Registrarse = () => {
	const navigate = useNavigate(); 
    const [nombre, setNombre] = useState('');
    const [apellido, setApellido] = useState('');
    const [edad, setEdad] = useState('');
    const [numeroDocumento, setNumeroDocumento] = useState('');
    const [mail, setEmail] = useState('');
    const [telefono, setTelefono] = useState('');
	const [contraseña, setContraseña] =useState('');
	const [username, setUsername] =useState('');
	const [myError, setMyError] = useState(false);
	const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');

	const handleUsernameChange = (e) => setUsername(e.target.value);
    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleApellidoChange = (e) => setApellido(e.target.value);
    const handleEdadChange = (e) => setEdad(e.target.value);
    const handleNumeroDocumentoChange = (e) => setNumeroDocumento(e.target.value);
    const handleMailChange = (e) => setEmail(e.target.value);
    const handleTelefonoChange = (e) => setTelefono(e.target.value);
	const handleContraseñaChange = (e) => setContraseña(e.target.value);

    const handleSubmit = async (e) => {
		
        e.preventDefault();
		console.log('Submit button clicked!');

		if (edad < 18){
			setMyError(true);
			setMsgError('Debes ser mayor de edad para registrarte.');
		} else if (contraseña.length < 6){
			setMyError(true);
			setMsgError('La contraseña debe tener por lo menos 6 caracteres');
		} else {
			console.log('entro');
			const formData = new FormData();
			formData.append('username', username);
			formData.append('nombre', nombre);
			formData.append('apellido', apellido);
			formData.append('edad', edad);
			formData.append('dni', numeroDocumento);
			formData.append('mail', mail);
			formData.append('telefono', telefono);
			formData.append('clave', contraseña);
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
				navigate("../IniciarSesion");
			} catch (error) {
				console.error('Error:', error.response.data.Mensaje);
				setMyError(true);
				setMsgError(error.response.data.Mensaje);
			}
		}
    };

    return (
	<>
	<h1>Trueca </h1>
	<div id="registrarse">
		<br/>
		<form onSubmit={handleSubmit}>
			<h3> Registrate para poder ofertar intercambios con los demás usuarios! </h3>  <br /> <br />
			<input placeholder='Nombre de usuario' type="text" value={username} onChange={handleUsernameChange} required /> <br/><br/> 

			<input placeholder='Nombre' type="text" value={nombre} onChange={handleNombreChange} required /> <br/><br/>  


			<input placeholder='Apellido' type="text" value={apellido} onChange={handleApellidoChange} required />  <br/><br/>  


			<input placeholder='Edad' type="text" value={edad} onChange={handleEdadChange} required />  <br/><br/>  


			<input placeholder='N° de DNI' type="text" value={numeroDocumento} onChange={handleNumeroDocumentoChange} required />  <br/><br/>  

	
			<input placeholder='Email' type="text"  value={mail} onChange={handleMailChange} required /> <br/> <br/> 
			

			<input placeholder='Telefono' type="text" value={telefono} onChange={handleTelefonoChange} />  <br/><br/> 
			

			<input placeholder='Contraseña' type="password" value={contraseña} onChange={handleContraseñaChange} required />  <br/><br/> 
			
			<ButtonSubmit text="Registrarse" />
		</form>
				{myError &&
					<p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
				}
	</div>
	</>
	
	);
};

export default Registrarse;