import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';
import axios from 'axios';

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
            const response = await axios.post("http://localhost:8000/public/newCentro", formData,
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
                <label>
					Ingrese el horario de apertura del centro: 
					<input type="text" value={nombre} onChange={handleNombreChange} required />
				</label>
				<br />
                <label>
					Ingrese el horario de cierre del centro: 
					<input type="text" value={nombre} onChange={handleNombreChange} required />
				</label>
				<br />
				<ButtonSubmit text="Agregar Centro!" />
			</form>
		</div>
    );
};

export default AgregarCentro;