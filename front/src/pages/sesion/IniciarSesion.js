import React, { useState } from 'react';
import { ButtonSubmit } from "../../components/ButtonSubmit";

const IniciarSesion = () => {

        const [user, setUser] = useState('');
        const [clave, setClave] = useState('');
    
        const handleUserChange = (e) => setUser(e.target.value);
        const handleClaveChange = (e) => setClave(e.target.value);
    
        const handleSubmit = async (e) => {
            e.preventDefault();
            console.log('Submit button clicked!');
    
            const formData = new FormData();
            formData.append('user', user);
            formData.append('clave', clave);
    
            try {
                const response = await fetch('/public/crearSesion', {
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
                        <input placeholder="Ingrese su usuario" type="text" value={user} onChange={handleUserChange} required /> 
                    </label>
                    <br />
                    <label>
                        <input placeholder="Ingrese su contraseña" type="password" value={clave} onChange={handleClaveChange} required />
                    </label>
                    <br />
                    <ButtonSubmit text="Iniciar sesión" />
                </form>
            </div>
        );
    };

export default IniciarSesion;