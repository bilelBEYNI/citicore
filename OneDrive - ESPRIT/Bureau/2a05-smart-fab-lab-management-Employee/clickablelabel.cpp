#include "clickablelabel.h"

clickablelabel::clickablelabel(QObject *parent)
    : QAbstractItemModel(parent)
{
}

QVariant clickablelabel::headerData(int section, Qt::Orientation orientation, int role) const
{
    // FIXME: Implement me!
}

QModelIndex clickablelabel::index(int row, int column, const QModelIndex &parent) const
{
    // FIXME: Implement me!
}

QModelIndex clickablelabel::parent(const QModelIndex &index) const
{
    // FIXME: Implement me!
}

int clickablelabel::rowCount(const QModelIndex &parent) const
{
    if (!parent.isValid())
        return 0;

    // FIXME: Implement me!
}

int clickablelabel::columnCount(const QModelIndex &parent) const
{
    if (!parent.isValid())
        return 0;

    // FIXME: Implement me!
}

QVariant clickablelabel::data(const QModelIndex &index, int role) const
{
    if (!index.isValid())
        return QVariant();

    // FIXME: Implement me!
    return QVariant();
}
