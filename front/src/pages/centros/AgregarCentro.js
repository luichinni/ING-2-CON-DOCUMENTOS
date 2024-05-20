import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';

const AgregarCentro = () => {
    const [nombre, setNombre] = useState('');
	const [direccion, setDireccion] = useState('');

    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleDireccionChange = (e) => setDireccion(e.target.value);

    const handleSubmit = async (e) => {
        e.preventDefault();
		console.log('Submit button clicked!');

        const formData = new FormData();
        formData.append('nombre', nombre);
		formData.append('direccion', direccion);

        try {
            const response = await fetch('/public/newCentro', {
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
		<div>
			<br /><br /><br /><br /><br /><br />
			<form onSubmit={handleSubmit}>
				<label>
					Ingrese el nombre del centro a agregar: 
					<input type="text" value={nombre} onChange={handleNombreChange} required />
				</label>
				<br />
				<label>
					Ingrese la dirección del centro: 
					<input type="text" value={direccion} onChange={handleDireccionChange} required />
				</label>
				<br />
				<ButtonSubmit text="Agregar Centro!" />
			</form>
		</div>
    );
};

export default AgregarCentro;