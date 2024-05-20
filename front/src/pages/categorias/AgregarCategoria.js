import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';

const AgregarCategoria = () => {
    const [nombre, setNombre] = useState('');

    const handleNombreChange = (e) => setNombre(e.target.value);


    const handleSubmit = async (e) => {
        e.preventDefault();
		console.log('Submit button clicked!');

        const formData = new FormData();
        formData.append('nombre', nombre);

        try {
            const response = await fetch('/public/newCategoria', {
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
			<br /><br /><br /><br /><br /><br /><br /><br />
			<form onSubmit={handleSubmit}>
				<label>
					Ingrese el nombre de la categoria a agregar: 
					<input type="text" value={nombre} onChange={handleNombreChange} required />
				</label>
				<br />
				<ButtonSubmit text="Agregar Categoria!" />
			</form>
		</div>
    );
};
export default AgregarCategoria;