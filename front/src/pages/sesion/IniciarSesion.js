import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';
import {Link} from 'react-router-dom';

const IniciarSesion = () => {

    const navigate = useNavigate();    
    const [username, setUsername] = useState('');
        const [clave, setClave] = useState('');
    
        const handleUsernameChange = (e) => setUsername(e.target.value);
        const handleClaveChange = (e) => setClave(e.target.value);
    
        const handleSubmit = async (e) => {
            e.preventDefault();
            console.log('Submit button clicked!');
    
            const formData = new FormData();
            formData.append('username', username);
            formData.append('clave', clave);
    
            try {
                
                const response = await axios.post("http://localhost:8000/public/crearSesion", formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
                console.log('Success:', response.data.token);
                localStorage.setItem('token',response.data.token);
                localStorage.setItem('username',username);
                console.log(username);
                navigate("../");
                window.location.reload();
                
            } catch (error) {
                console.error('Error:', error);
            }
        };
    
        return (
            <div>
                <br /><br /><br /><br /><br /><br />
                <form onSubmit={handleSubmit}>
                    <input placeholder="Ingrese su usuario" type="text" value={username} onChange={handleUsernameChange} required />
                    <br/> <br />
                    <input placeholder="Ingrese su contraseña" type="password" value={clave} onChange={handleClaveChange} required /> 
                    <br/> <br/>
                    <button type="submit" className="botonSubmit"> Iniciar sesión</button>
                </form>
                <br/><br/>
                <label calssName="registrarse"> 
                <p className='textRegistrarse'>
                    ¿No tienes un usuario?
                    <Link to="/Registrarse" className='botonRegistrarse'> Regístrate </Link>
                </p>
                </label>
            </div>
        );
    };

export default IniciarSesion;