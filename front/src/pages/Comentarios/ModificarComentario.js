import { useEffect, useState } from "react";
import axios from 'axios';
import { ButtonSubmit } from "../../components/ButtonSubmit";
import { useParams } from "react-router-dom";


const ModificarComentario = (props) =>{
    const [Coment, setComent] = useState('');
    const idComent = useParams();
    const userMod = useParams();
    const user = localStorage.getItem('username')
    const [myError, setMyError] = useState(false);
    const [msgError, setMsgError] = useState('No deberías estar viendo este mensaje');
    const [comentarios, setComentarios] = useState([]);
    const [error, setError] = useState('');

    const handleComentarioChange = (e) => setComent(e.target.value);

    const fetchData = async () => {
        setError('');
  
        try {
          const url = `http://localhost:8000/public/listarComentarios?id=`;
          console.log(`url: ${url}`)
          const response = await axios.get(url);
          console.log(`respuesta: ${response.data}`)
  
          if (response.data.length === 0) {
            setError('No se puede modificar el comentario');
            setComentarios([]);
            console.log(`falle por 0 resultados`)
          } else {
            const Comentario = procesar (response.data)[0];
            setComent(Comentario.texto)
            console.log(`comentarios: ${comentarios}`)
          }
        } catch (error) {
          setError('No se puede modificar el comentario');
          console.log(`falle por error`)
          console.error(error);
        }
      };
      useEffect(() => {
      fetchData();
    }, []);


    const handleSubmit = async(e) =>{
        e.preventDefault()
        const formData = new FormData();

        formData.append('id', idComent);
        formData.append('userMod', userMod);
        formData.append('settexto', Coment);
        console.log(`comentario: ${Coment}`)
        console.log(`publicacion: ${formData.get('publicacion')}`);
        console.log(`user: ${formData.get('user')}`);
        console.log(`texto: ${formData.get('texto')}`);

        try {
            const response = await axios.put("http://localhost:8000/public/updateComentario", formData,
                {
                    headers: {
                        "Content-Type": "application/json",
                    },
                });
            console.log('realizado:', response);
            window.location.reload();
        } catch (error) {
            setMyError(true);
            setMsgError(error.response.data.Mensaje);
        }
    }

    function procesar(comentarios) {
        let comentCopy = [];
        Object.keys(comentarios).forEach(function (clave) {
          if (!isNaN(clave)) {
            comentCopy[clave] = comentarios[clave]
          }
        })
        return comentCopy
      }

    return(
        <fieldset>
            <form onSubmit={handleSubmit}>
            <h2>modificar Comentario</h2>
            <input type="text" value={Coment} onChange={handleComentarioChange} placeholder="Ingrese Comentario" required />
            <ButtonSubmit text="modificar"/>
            </form>
            {myError &&
                <p style={{ backgroundColor: "red", color: "white", textAlign: "center" }}>{msgError}</p>
            }
        </fieldset>
    )
}
export default ModificarComentario;