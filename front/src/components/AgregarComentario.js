import { useState } from "react";
import axios from 'axios';
import { ButtonSubmit } from "./ButtonSubmit";


const AgregarComentario = (props) =>{
    const [Coment, setComent] = useState('');
    const user = localStorage.getItem('username')
    const [myError, setMyError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');

    const handleComentarioChange = (e) => setComent(e.target.value);

    const handleSubmit = async(e) =>{
        e.preventDefault()
        const formData = new FormData();

        formData.append('publicacion', props.publicacion);
        formData.append('user', user);
        formData.append('texto', Coment);
        console.log(`comentario: ${Coment}`)
        console.log(`publicacion: ${formData.get('publicacion')}`);
        console.log(`user: ${formData.get('user')}`);
        console.log(`texto: ${formData.get('texto')}`);

        try {
            const response = await axios.post("http://localhost:8000/public/newComentario", formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
            console.log('realizado:', response);
            
        } catch (error) {
            setMyError(true);
            setMsgError(error.response.data.Mensaje);
        }
    }


    return(
        <fieldset>
            <form onSubmit={handleSubmit}>
            <h2>Agregar Comentario</h2>
            <input type="text" value={Coment} onChange={handleComentarioChange} placeholder="Ingrese Comentario" required />
            <ButtonSubmit text="comentar"/>
            </form>
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
        </fieldset>
    )
}
export default AgregarComentario;