import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';

const Registrarse = () => {
    const [nombre, setNombre] = useState('');
    const [apellido, setApellido] = useState('');
    const [edad, setEdad] = useState('');
    const [numeroDocumento, setNumeroDocumento] = useState('');
    const [mail, setEmail] = useState('');
    const [telefono, setTelefono] = useState('');
	const [contraseña, setContraseña] =useState('');

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
        formData.append('nombre', nombre);
        formData.append('apellido', apellido);
		formData.append('edad', edad);
		formData.append('numeroDocumento', numeroDocumento);
		formData.append('mail', mail);
		formData.append('telefono', telefono);
		formData.append('contraseña', contraseña);

        try {
            const response = await fetch('/public/newUsuario', {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();
            console.log('Success:', result);
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
			<label id="formtext" >Nombre </label>
			<br/>
			<input type="text" NameClass="registrarse" value={nombre} onChange={handleNombreChange} required />
			<br/>
			
			<label id="formtext" >Apellido </label>
			<br/>
			<input type="text" NameClass="registrarse" value={apellido} onChange={handleApellidoChange} required /> 
			<br/>
			
			<label id="formtext" >Edad </label>
			<br/>
			<input type="text" NameClass="registrarse" value={edad} onChange={handleEdadChange} required /> 
			<br/>
			
			<label id="formtext" >N° de DNI </label>
			<br/>
			<input type="text" NameClass="registrarse" value={numeroDocumento} onChange={handleNumeroDocumentoChange} required /> 
			<br/>
			
			<label id="formtext" >Email </label>
			<br/>
			<input type="text" NameClass="registrarse"  value={mail} onChange={handleMailChange} required />
			<br/>
			
			<label id="formtext" >Telefono </label>
			<br/>
			<input type="text" NameClass="registrarse" value={telefono} onChange={handleTelefonoChange} /> 
			<br/>
			
			<label id="formtext" >Contraseña </label>
			<br/>
			<input type="text" NameClass="registrarse" value={contraseña} onChange={handleContraseñaChange} required /> 
			<br/>
			
			<ButtonSubmit text="Registrarse" />
		</form>
	</div>
	</>
	
	);
};

export default Registrarse;