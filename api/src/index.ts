import 'dotenv/config';
import express from 'express';
import cors from 'cors';
import { requireAuth } from './middleware/auth';

const app = express();
const port = Number(process.env.PORT) || 3000;

app.use(cors());
app.use(express.json());

app.get('/health', (_req, res) => {
  res.json({ status: 'ok' });
});

app.get('/me', requireAuth, (req, res) => {
  res.json({ user: req.user });
});

app.listen(port, () => {
  console.log(`API listening on port ${port}`);
});
