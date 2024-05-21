import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';
import axios from 'axios';

const Registrarse = () => {
    const [nombre, setNombre] = useState('');
    const [apellido, setApellido] = useState('');
    const [edad, setEdad] = useState('');
    const [numeroDocumento, setNumeroDocumento] = useState('');
    const [mail, setEmail] = useState('');
    const [telefono, setTelefono] = useState('');
	const [contraseña, setContraseña] =useState('');
	const [username, setUsername] =useState('');

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
            const response = await axios.post("http://localhost:8000/public/newUsuario", formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
            console.log('Success:', response);
        } catch (error) {
            console.error('Error:', error);
        }
    };

    return (
	<>
	<h1>Trueca </h1>
	<div id="registrarse">
		<br/>
		<p> Registrate para poder ofertar intercambios con los demás usuarios! </p>
		<form onSubmit={handleSubmit}>
			<label id="formtext" >Nombre de Usuario </label> <br/>
			<input placeholder='Nombre de usuario' type="text" value={username} onChange={handleUsernameChange} required /> <br/>

			<label id="formtext" >Nombre </label> <br/>
			<input placeholder='Nombre' type="text" value={nombre} onChange={handleNombreChange} required /> <br/> 

			<label id="formtext" >Apellido </label> <br/>
			<input placeholder='Apellido' type="text" value={apellido} onChange={handleApellidoChange} required />  <br/> 

			<label id="formtext" >Edad </label> <br/>
			<input placeholder='Edad' type="text" value={edad} onChange={handleEdadChange} required />  <br/> 

			<label id="formtext" >N° de DNI </label> <br/>
			<input placeholder='N° de DNI' type="text" value={numeroDocumento} onChange={handleNumeroDocumentoChange} required />  <br/> 

			<label id="formtext" >Email </label> <br/>
			<input placeholder='Email' type="text"  value={mail} onChange={handleMailChange} required /> <br/> 
			
			<label id="formtext" >Telefono </label> <br/>
			<input placeholder='Telefono' type="text" value={telefono} onChange={handleTelefonoChange} />  <br/>
			
			<label id="formtext" >Contraseña </label> <br/>
			<input placeholder='Contraseña' type="text" value={contraseña} onChange={handleContraseñaChange} required />  <br/>
			
			<ButtonSubmit text="Registrarse" />
		</form>
	</div>
	</>
	
	);
};

export default Registrarse;