import React from 'react'
import ReactDom from 'react-dom/client'
import { Header } from '../componets/header'
import { ListarPublis } from './ListarPublis'

const root = ReactDom.createRoot(document.getElementById('root'))
root.render(inicio())
function inicio() {
    return <>
    <Header />
    <ListarPublis />
    

    </>
}