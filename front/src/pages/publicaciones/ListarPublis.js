import axios from 'axios';
import Tarjeta from './Tarjeta';
import { ListaDePublicaciones } from './ListaDePublicaciones';
import '../../HarryStyles/Publicaciones.css';
import { useEffect, useState } from 'react';

const ListarPublis = () => {
  const [Publi, setPubli] = useState([]);
  const [parametros, setParametros] = useState({
        nombre: "",
        user: "",
        categoria: "",
        estado: "",
        id: ""
  })

  useEffect(() => {
    const fetchData = async () => {
      try {
        const queryParams = new URLSearchParams(parametros);
        const url = `https://localhost:8000/public/listarPublicaciones?${queryParams.toString()}`;
        const response = await axios.get(url);

        if (response.data.length === 0){
          setError('No hay publicaciones disponibles')
          return <h1 className="SinPubli">¡No hay publicaciones disponibles en este momento!</h1>;
         } else {
          setPubli(response.data);
          setError('');
         }

        console.log(response.data);
      } catch (error) {
        setError('Ocurrio un error al obtener las publicaciones.');
        console.log(error);
      }
    };
  
    fetchData();
  }, [parametros]);
  
const handleParametrosChange = (newParametros) => {
    setParametros(newParametros);
};

  const filasDePublicaciones = [];
  for (let i = 0; i < ListaDePublicaciones.length; i += 3) {
    filasDePublicaciones.push(ListaDePublicaciones.slice(i, i + 3));
  }
  
  return (
    <div className="ListadoPublis">
      {filasDePublicaciones.map((fila, index) => (
        <div key={index} className="fila">
          {fila.map(publicacion => (
            <div key={publicacion.ID} className="publicacion">
              <Tarjeta publicacion={publicacion} />
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}

export default ListarPublis;