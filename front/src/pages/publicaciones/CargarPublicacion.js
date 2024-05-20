import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';

const AgregarPublicacion = () => {
    const [nombre, setNombre] = useState('');
    const [descripcion, setDescripcion] = useState('');
    const [fotos, setFotos] = useState([]);
    const [categoria, setCategoria] = useState('');
    const [centros, setCentros] = useState([]);

    const handleNombreChange = (e) => setNombre(e.target.value);
    const handleDescripcionChange = (e) => setDescripcion(e.target.value);
    const handleFotosChange = (e) => setFotos(e.target.files);
    const handleCategoriaChange = (e) => setCategoria(e.target.value);
    const handleCentrosChange = (e) => {
        const selectedValues = Array.from(e.target.selectedOptions, option => option.value);
        setCentros(selectedValues);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
		console.log('Submit button clicked!');

        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        Array.from(fotos).forEach((file, index) => {
            formData.append(`fotos[${index}]`, file);
        });
        formData.append('categoria', categoria);
        centros.forEach((centro, index) => {
            formData.append(`centros[${index}]`, centro);
        });

        try {
            const response = await fetch('/public/newPublicacion', {
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
					Nombre del producto: 
					<input type="text" value={nombre} onChange={handleNombreChange} />
				</label>
				<br />
				<label>
					Descripción del producto: 
					<textarea value={descripcion} onChange={handleDescripcionChange}></textarea>
				</label>
				<br />
				<label>
					Seleccione las fotos, por lo menos una: 
					<input type="file" accept="image/*" multiple onChange={handleFotosChange} />
				</label>
				<br />
				<label>
					Categoría:
					<select value={categoria} onChange={handleCategoriaChange}>
						<option value="Alimentos">Alimentos</option>
						<option value="ArtLimpieza">Articulos de Limpieza</option>
						<option value="Ropa">Ropa</option>
						<option value="UtilesEscolares">Utiles escolares</option>
					</select>
				</label>
				<br /><br />
				<label>
					Centros:
					<select multiple onChange={handleCentrosChange}>
						<option value="centro1">Centro 1</option>
						<option value="centro2">Centro 2</option>
						<option value="centro3">Centro 3</option>
					</select>
				</label>
				<br />
				<ButtonSubmit text="Subir producto!" />
			</form>
		</div>
    );
};

export default AgregarPublicacion;
