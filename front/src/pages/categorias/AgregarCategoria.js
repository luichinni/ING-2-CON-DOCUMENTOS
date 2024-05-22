import { ButtonSubmit } from "../../components/ButtonSubmit";
import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';

const AgregarCategoria = () => {
    const navigate = useNavigate();
    const [nombre, setNombre] = useState('');
    const [myError, setMyError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');

    const handleNombreChange = (e) => setNombre(e.target.value);


    const handleSubmit = async (e) => {
        e.preventDefault();
		console.log('Submit button clicked!');

        const formData = new FormData();
        formData.append('nombre', nombre);

        try {
            const response = await axios.post("http://localhost:8000/public/newCategoria", formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
            console.log('Success:', response);
            navigate ("../Categorias");
            window.location.reload();
        } catch (error) {
            console.error('Error:', error);
            setMyError(true);
            setMsgError(error.response.data.Mensaje);
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
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
		</div>
    );
};
export default AgregarCategoria;