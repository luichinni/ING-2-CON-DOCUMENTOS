import React from 'react';
import '../../HarryStyles/Publicaciones.css';
import '../../HarryStyles/styles.css';
import ListarPubliInter from './listarPubliInter';


const MisPublis = () => {
  return (
    <div className='dashboard'>
      <br /><br /><br /><br /><br /><br />
      <div className='banner'>
        <h1>Seleccione el producto que desea intercambiar:</h1>
      </div>

      <ListarPubliInter/>
    </div>
  );
}

export default MisPublis;