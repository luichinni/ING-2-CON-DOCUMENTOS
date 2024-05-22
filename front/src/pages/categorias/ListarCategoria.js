import axios from 'axios';
import {Link} from 'react-router-dom';
import Categoria from '../../components/Categoria';
import '../../HarryStyles/categorias.css';
import '../../HarryStyles/styles.css'
import { useEffect, useState } from 'react';

const ListarCategoria = () => {
  const [categorias, setCategorias] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [parametros] = useState({
    id: "",
    nombre: "",
  });

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');

      try {
        const queryParams = new URLSearchParams(parametros).toString();
        const url = `http://localhost:8000/public/listarCategorias?${queryParams}`;
        const response = await axios.get(url);

        if (response.data.length === 0) {
          setError('No hay categorias disponibles');
          setCategorias([]); 
        } else {
          setCategorias(procesar(response.data));
        }
      
      } catch (error) {
        setError('Ocurrió un error al obtener las categorias.');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [parametros]);

  function procesar(categorias) {
    let centroCopy = [];
    Object.keys(categorias).forEach(function (clave) {
      if (!isNaN(clave)) {
        centroCopy[clave] = categorias[clave]
      }
    })
    return centroCopy
  }

  return (
    <div className='Content'>
      <br /><br /><br /><br /><br /><br /><br />
      <div className='Publi-Div'>
        <div className='botonAgregar'>
            <Link 
              to="/AgregarCategoria"
              className="agregarBoton"> 
              Agregar Categoria
            </Link>
        </div>
        {loading ? (
          <h1 className='Cargando'>Cargando...</h1>
        ) : error ? (
          <h1 className='SinCentros'>{error}</h1>
        ) : (
          categorias.map(categoria => (
            <Categoria
                Id = {categoria.id}
                Nombre = {categoria.Nombre}
            />
          ))
        )}
      </div>
    </div>
  );
}

export default ListarCategoria;